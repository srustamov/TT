<?php namespace System\Engine;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use ArrayAccess;
use App\Kernel;
use System\Facades\Route;
use System\Facades\Config;
use System\Facades\Http;
use System\Libraries\Benchmark;
use System\Engine\Http\Middleware;

class App implements ArrayAccess
{

    protected $middleware = [
        \System\Engine\Http\Middleware\LoadSettingVariables::class ,
        \System\Engine\Http\Middleware\PrepareConfigs::class ,
        \System\Engine\Http\Middleware\RegisterExceptionHandler::class ,
        \System\Engine\Http\Middleware\StartSession::class ,
    ];

    protected $custom_middleware = [];

    protected $bootstrap = false;

    protected $application_path;

    protected $public_path;

    protected $storage_path   = 'storage';

    protected $lang_path = 'lang';

    protected $configs_path   = 'app/Config';

    protected $settings_file  = '.settings';

    protected $configs_cache_file   = 'storage/system/configs.php';

    protected $routes_cache_file    = 'storage/system/routes.php';

    protected $settings_cache_file  = 'storage/system/settings';

    private static $instance;


    /**
     * App constructor.
     * Set application base path
     *
     * @param null $basePath
     */
    function __construct ( $basePath = null )
    {
        if(!defined('CONSOLE')) {
          define('CONSOLE',(php_sapi_name() == 'cli' || php_sapi_name() == 'phpdbg'));
        }

        if (is_null ( $basePath ))
        {
            $this->application_path = dirname ( dirname ( __DIR__ ) );
        }
        else
        {
            $this->application_path = rtrim($basePath, DIRECTORY_SEPARATOR);
        }

        chdir($this->application_path);

        static::$instance = &$this;

        return $this;
    }

    /**
     * Application bootstrapping
     *
     * @return $this
     */
    public function bootstrap ()
    {
        if(!$this->bootstrap)
        {
          $this->setPublicPath ();

          $this->registerMiddleware ($this->middleware);

          $this->setLocale ();

          $this->registerMiddleware ($this->custom_middleware);

          $this->setAliases ();

          Load::register('app', $this);

          $this->bootstrap = true;
        }

        return $this;
    }


    public function makeMiddleware($middleware)
    {
      if(!$this->bootstrap) {

        if(is_object($middleware)) {

          $middleware = get_class($middleware);
        }

        array_push($this->custom_middleware, $middleware);
      }
    }


    protected function registerMiddleware ($middleware_array)
    {
        foreach ($middleware_array as $middleware) {
            Middleware::init ($middleware , true );
        }
    }

    protected function setAliases ()
    {
        $aliases = Config::get ( 'aliases' , [] );

        $aliases['app'] = get_class( $this );

        foreach ($aliases as $key => $value) {
            class_alias ( '\\' . $value , $key );
        }
    }

    protected function setLocale ()
    {
        setlocale ( LC_ALL , Config::get ('datetime.setLocale'));

        date_default_timezone_set (Config::get ('datetime.time_zone', 'UTC'));
    }

    public function callAppKernel ()
    {
        $kernel = new Kernel();

        if (property_exists ( $kernel , 'middleware' ))
        {
            foreach ($kernel->middleware as $middleware)
            {
                Middleware::init ( $middleware , true );
            }
        }

        if (method_exists ( $kernel , 'boot' ))
        {
            $kernel->boot ();
        }

        return $this;
    }

    public function routing ()
    {
        Route::execute($this);

        return $this;
    }

    public function response ()
    {
        return Load::class( 'response' );
    }

    public function benchmark ( $finish )
    {
        if (CONSOLE || !Config::get ('app.debug') || Http::isAjax()) 
        {
            return null;
        }

        $this->response()->appendContent(Benchmark::table ( $finish ));
      
    }

    public function setPublicPath (String $path = null)
    {
        if(!is_null($path))
        {
            $this->public_path = $path;
        }
        else
        {
            if (isset( $_SERVER[ 'SCRIPT_FILENAME' ] ) && !empty( $_SERVER[ 'SCRIPT_FILENAME' ] ))
            {
                $parts = explode ( '/' , $_SERVER[ 'SCRIPT_FILENAME' ] );

                array_pop($parts);

                $this->public_path = implode ( '/' , $parts );
            }
            else
            {
                $this->public_path = $this->application_path . DIRECTORY_SEPARATOR . 'public';
            }
        }
    }

    public function setStoragePath(String $path)
    {
        $this->storage_path = trim($path,DIRECTORY_SEPARATOR);
    }

    public function setConfigsPath(String $path)
    {
        $this->configs_path = trim($path,DIRECTORY_SEPARATOR);
    }

    public function setLangPath(String $path)
    {
        $this->lang_path = trim($path,DIRECTORY_SEPARATOR);
    }

    public function setSettingsFile(String $file)
    {
        $this->settings_file = $file;
    }

    public function settingsFile()
    {
        return $this->path($this->settings_file);
    }

    public function public_path($path = '')
    {
        return $this->public_path.DIRECTORY_SEPARATOR.(ltrim($path,DIRECTORY_SEPARATOR)) ;
    }

    public function path($path = '')
    {
        return $this->application_path.DIRECTORY_SEPARATOR.(ltrim($path,DIRECTORY_SEPARATOR));
    }

    public function storage_path($path = '')
    {
        return $this->path($this->storage_path.DIRECTORY_SEPARATOR.(ltrim($path,DIRECTORY_SEPARATOR)));
    }

    public function configs_path($path = '')
    {
        return $this->path($this->configs_path.DIRECTORY_SEPARATOR.(ltrim($path,DIRECTORY_SEPARATOR)));
    }

    public function app_path($path = '')
    {
        return $this->application_path
            .DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR
            .ltrim($path,DIRECTORY_SEPARATOR);
    }


    public function lang_path($path = '')
    {
      return $this->path($this->lang_path.DIRECTORY_SEPARATOR.(ltrim($path,DIRECTORY_SEPARATOR)));
    }

    public function configs_cache_file(String $file = null)
    {
        if(!is_null($file)) {
            $this->configs_cache_file = $file;
        } else {
            return $this->path($this->configs_cache_file);
        }
    }

    public function routes_cache_file(String $file = null)
    {
        if(!is_null($file)) {
            $this->routes_cache_file = $file;
        } else {
            return $this->path($this->routes_cache_file);
        }
    }

    public function settings_cache_file(String $file = null)
    {
        if(!is_null($file)) {
            $this->settings_cache_file = $file;
        } else {
            return $this->path($this->settings_cache_file);
        }
    }


    public function classes(String $name = null,Bool $isValue = false)
    {
      $classes = array(
          'array' => 'System\Libraries\Arr',
          'authentication' => 'System\Libraries\Auth\Authentication',
          'cache' => 'System\Libraries\Cache\Cache',
          'console' => 'System\Engine\Cli\Console',
          'cookie' => 'System\Libraries\Cookie',
          'database' => 'System\Libraries\Database\Database',
          'email' => 'System\Libraries\Mail\Email',
          'file' => 'System\Libraries\File',
          'hash' => 'System\Libraries\Hash',
          'html' => 'System\Libraries\Html',
          'http' => 'System\Libraries\Http',
          'input' => 'System\Libraries\Input',
          'lang' => 'System\Libraries\Language',
          'language' => 'System\Libraries\Language',
          'middleware' => 'System\Engine\Http\Middleware',
          'openssl' => 'System\Libraries\Encrypt\OpenSsl',
          'redirect' => 'System\Libraries\Redirect',
          'redis' => 'System\Libraries\Redis',
          'request' => 'System\Engine\Http\Request',
          'response' => 'System\Engine\Http\Response',
          'route' => 'System\Engine\Http\Routing\Route',
          'session' => 'System\Libraries\Session\Session',
          'str' => 'System\Libraries\Str',
          'string' => 'System\Libraries\Str',
          'storage' => 'System\Libraries\Storage',
          'url' => 'System\Libraries\Url',
          'validator' => 'System\Libraries\Validator',
          'view' => 'System\Libraries\View\View',
      );

      if(is_null($name)) {
          return $classes;
      }

      if(!$isValue) {
          return $classes[strtolower($name)] ?? false;
      } else {
          return array_search($name,$classes);
      }
    }

    public static function instance()
    {
        return static::$instance;
    }


    public static function end()
    {
        if(function_exists('fastcgi_finish_request'))
        {
            fastcgi_finish_request();
        }

        exit();
    }



    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet ( $offset )
    {
        return Load::class($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet ( $offset , $value )
    {
        Load::register($offset,$value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset ( $offset )
    {
      $load = Load::instance();

      unset($load[$offset]);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists ( $offset )
    {
      return array_key_exists($offset, Load::instance());
    }



}
