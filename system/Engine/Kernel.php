<?php namespace System\Engine;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */




ini_set ( 'display_errors' , 1 );

error_reporting(1);


use System\Core\Load;
use System\Facades\Route;

class Kernel
{

    protected $basePath;


    /**
     * @param null $basePath
     */
    public static function start ( $basePath = null )
    {
        $instance = new static();

        if (is_null ( $basePath )) {
            $instance->basePath = dirname ( dirname ( __DIR__ ) );
        } else {
            $instance->basePath = $basePath;
        }

        $instance->setPathDefines ();

        require_once BASEPATH . '/system/Engine/helpers.php';

        Load::settingVariables ();

        define('APP_DEBUG',Load::config('config.debug'));

        set_exception_handler ( '\System\Engine\Exception\TTException::handler');

        $instance->loadHelpers ();

        $instance->setAliases ();

        setlocale ( LC_ALL , config ( 'datetime.setLocale' ) );

        date_default_timezone_set ( config ( 'datetime.time_zone' , 'UTC' ) );

        $instance->callApplicationKernel();

        $instance->callRoutes ();
    }


    public function callApplicationKernel()
    {
        if (class_exists ( '\App\Kernel' )) {
            $kernel = new \App\Kernel();

            $_middleware = $kernel->middleware;

            foreach ($_middleware as $middleware) {
                call_user_func_array ( [ new $middleware() , 'handle' ] , [ new \System\Engine\Http\Request() , null ] );
            }
            if (method_exists($kernel,'boot')) {
                $kernel->boot ();
            }

        }
    }


    /**
     * @param null $publicPath
     */
    public function setPublicPath ( $publicPath = null )
    {
        if (is_null ( $publicPath )) {
            if (!defined ( 'PUBLIC_DIR' )) {
                if (isset( $_SERVER[ 'SCRIPT_FILENAME' ] ) && !empty( $_SERVER[ 'SCRIPT_FILENAME' ] )) {
                    $_ = explode ( '/' , $_SERVER[ 'SCRIPT_FILENAME' ] );
                    array_pop ( $_ );
                    define ( 'PUBLIC_DIR' , implode ( '/' , $_ ) );
                } else {
                    define ( 'PUBLIC_DIR' , BASEPATH . DS . 'public' );
                }
            }
        } else {
            if (!defined ( 'PUBLIC_DIR' )) {
                define ( 'PUBLIC_DIR' , $publicPath );
            }
        }
    }


    /**
     *@var $aliases
     */
    public function setAliases ()
    {
        $aliases = config ( 'aliases' , [] );

        foreach ($aliases as $key => $value) {
            class_alias ( '\\' . $value , $key );
        }
    }



    public function callRoutes ()
    {
        if (!InConsole ()) {
            import_dir_files ( path ( 'routes' ) );
            Route::execute ();
        }
    }


    public function loadHelpers ()
    {
        import ( path ( 'system/Core/Helpers.php' ) );

        import_dir_files ( path ( 'app/Helpers' ) );
    }



    /**
     * @param null $path
     */
    private function setPathDefines ( $path = null )
    {
        if (!is_null ( $path )) {
            $this->basePath = $path;
        }

        define ( 'BASEPATH' , $this->basePath );

        define ( 'DS' , DIRECTORY_SEPARATOR );

        $this->setPublicPath ();

        chdir ( BASEPATH . DS );
    }


}
