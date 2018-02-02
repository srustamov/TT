<?php namespace System\Engine;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */


error_reporting(E_ALL);

ini_set('display_errors', 0);

use System\Engine\Exception\CustomException;

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

        $this->setChdir(BASEDIR.DS);
    }


    public static function start($basePath = null)
    {
        (new static)->setInstance();

        set_exception_handler(function ($e)
        {
            CustomException::handler($e);
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

        import_dir_files(BASEDIR.'/routes');



        if (!InConsole()) \Route::init();

				return true;
    }


    public function setAliases()
    {
        foreach (config('aliases', []) as $key => $value)
        {
            class_alias('\\'.$value, $key);
        }
    }


    public function setChdir($path)
    {
        chdir($path);
    }



    public function set_settings_variables()
    {
        $settingsFile = path('storage/system/settings');

        if (!file_exists($settingsFile))
        {
          touch($settingsFile);
          touch(path('.settings'),time() + 10);
        }

        if (filemtime($settingsFile) < filemtime(path('.settings')))
        {
            $_auto_detect = ini_get('auto_detect_line_endings');

            ini_set('auto_detect_line_endings', 1);

            $lines = file(BASEDIR.'/.settings', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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

                    $_settings[$name] = $value;
                }
            }



            foreach ($_settings as $key => $value)
            {
                if (strpos($value, '$') !== false)
                {
                    $value = preg_replace_callback('/\${([a-zA-Z0-9_\-.]+)}/',
                                function ($m) use ($_settings)
                                {
                                    return $_settings[$m[1]] ?? "${".$m[1]."}";
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
            $_settings = unserialize(file_get_contents(path('storage/system/settings')));

            foreach ($_settings as $key => $value)
            {
                $_ENV[$key] = $value;
            }
        }
    }


    public function loadHelpers()
    {
        require_once SYSDIR.'Engine/Helpers.php';

        import(SYSDIR.'Core/Helpers.php');

        import_dir_files(APPDIR.'Helpers');

    }


		private  function setInstance()
		{
			self::$instance = &$this;
		}
}
