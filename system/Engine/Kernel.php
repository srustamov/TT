<?php namespace System\Engine;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */


error_reporting(E_ALL);

ini_set('display_errors', 0);

use System\Engine\Exception\TTException;

class Kernel
{


    private $basePath;


    private static $instance;



    private function setPathDefines($path = null)
    {
        if (!is_null($path))
        {
            $this->basePath = $path;
        }

        define('BASEDIR', $this->basePath);

        define('DS', DIRECTORY_SEPARATOR);

        define('PS', PATH_SEPARATOR);

        define('APPDIR', BASEDIR.DS.'app'.DS);

        define('SYSDIR', BASEDIR.DS.'system'.DS);

        if (!defined('PUBLIC_DIR'))
        {
            if(isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME']))
            {
                $_ = explode('/',$_SERVER['SCRIPT_FILENAME']); array_pop($_);

                define('PUBLIC_DIR',implode('/',$_));

            }
            else
            {
                define('PUBLIC_DIR',BASEDIR.DS.'public');
            }

        }

        chdir(BASEDIR.DS);
    }


    public static function start($basePath = null)
    {
        self::setInstance();

        set_exception_handler(function ($e)
        {
            TTException::handler($e);
        });

        if (is_null($basePath))
        {
            self::$instance->basePath = dirname(dirname(__DIR__));
        }
        else
        {
            self::$instance->basePath = $basePath;
        }

        self::$instance->setPathDefines();

        self::$instance->loadHelpers();

        self::$instance->set_settings_variables();

        self::$instance->setAliases();

        setlocale(LC_ALL, config('datetime.setLocale'));

        date_default_timezone_set(config('datetime.time_zone', 'UTC'));


        if (class_exists('\App\Kernel'))
        {
            $kernel = new \App\Kernel();

            $_middleware = $kernel->middleware;

            foreach ($_middleware as $middleware)
            {
                (new $middleware())->handle(new \System\Engine\Http\Request(),null);
            }

            $kernel->boot();
        }

        if (!InConsole()) {
              import_dir_files(path('routes'));
             \Route::init ();
        }

    }


    public function setAliases()
    {
        foreach (config('aliases', []) as $key => $value)
        {
            class_alias('\\'.$value, $key);
        }
    }




    public function set_settings_variables()
    {
        $settingsFile = path('storage/system/settings');

        if (!file_exists($settingsFile))
        {
            touch($settingsFile);

            touch(path('.settings'),time() + 10);
        }

        if (!inConsole() && filemtime($settingsFile) < filemtime(path('.settings')))
        {
            $_auto_detect = ini_get('auto_detect_line_endings');

            ini_set('auto_detect_line_endings', 1);

            $lines = file(path('.settings'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            ini_set('auto_detect_line_endings', $_auto_detect);

            $_settings = [];

            foreach ($lines as $line)
            {
                $line = trim($line);

                if (isset($line[0]) && $line[0] === '#')
                {
                    continue;
                }

                if (strpos($line, '=') !== false)
                {
                    list($name, $value) = array_map('trim', explode('=', $line, 2));

                    if (preg_match('/\s+/', $value) > 0)
                    {
                        show_error("setting variable value containing spaces must be surrounded by quotes");
                    }

                    if (strtolower($value) == 'true')
                    {
                        $value = true;
                    }
                    if (strtolower($value) == 'false')
                    {
                        $value = false;
                    }

                    $_settings[$name] = $value;
                }
            }


            foreach ($_settings as $key => $value)
            {

                if (strpos($value, '$') !== false)
                {
                    $value = preg_replace_callback('/\${([a-zA-Z0-9_\-.\'\"\[\]]+)}/',
                        function ($m) use ($_settings)
                        {

                            if (isset($_settings[$m[1]]))
                            {
                                return $_settings[$m[1]];
                            }
                            else
                            {
                                if (($pos = strpos($m[1],'[')) !== false)
                                {

                                    $_global = substr($m[1],0,$pos);
                                    $item = str_replace(['["','[\'','"]','\']'],'',substr($m[1],$pos));

                                    switch ($_global)
                                    {
                                        case "_SERVER":
                                            $_global = $_SERVER;
                                            break;
                                        case "_REQUEST":
                                            $_global = $_REQUEST;
                                            break;
                                        case "_GET":
                                            $_global = $_GET;
                                            break;
                                        case "_POST":
                                            $_global = $_POST;
                                            break;
                                        case "_SESSION":
                                            $_global = $_SESSION;
                                            break;
                                        case "_COOKIE":
                                            $_global = $_COOKIE;
                                            break;
                                        case "_ENV":
                                            $_global = $_ENV;
                                            break;
                                        default:
                                            $_global = array();
                                            break;
                                    }

                                    return $_global[$item] ?? '${'.$m[1].'}';

                                }
                                else
                                {
                                    return ${"$m[1]"} ?? '${'.$m[1].'}';
                                }
                            }
                        },
                        $value
                    );
                }

                $_ENV[$key] = $value;
            }

            file_put_contents(path('storage/system/settings'), serialize($_ENV));
        }
        else
        {

            $_settings = (array) unserialize(file_get_contents(path('storage/system/settings')));

            foreach ($_settings as $key => $value)
            {
                $_ENV[$key] = $value;
            }
        }
    }


    public function loadHelpers()
    {
        require_once SYSDIR.'Engine'.DS.'Helpers.php';

        import(SYSDIR.'Core'.DS.'Helpers.php');

        import_dir_files(APPDIR.'Helpers');

    }


    private static function setInstance()
    {
        self::$instance = new static();
    }
}
