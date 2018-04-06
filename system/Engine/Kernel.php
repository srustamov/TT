<?php namespace System\Engine;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */





use System\Facades\Load;
use System\Facades\Route;
use System\Libraries\Benchmark;
use System\Exceptions\TTException;
use System\Engine\Http\Middleware;


class Kernel
{

    protected $basePath;


    function __construct($basePath = null)
    {
      if (is_null ( $basePath ))
      {
          $this->basePath = dirname ( dirname ( __DIR__ ) );
      } else {
          $this->basePath = $basePath;
      }
      return $this;
    }


    public function bootstrap ()
    {

        $this->setPathDefines ();

        require_once __DIR__.'/helpers.php';

        Load::settingVariables ();

        $exception = new TTException;

        $exception->register();

        $this->loadHelpers ();

        $this->setAliases ();

        setlocale ( LC_ALL , Load::config ( 'datetime.setLocale' ) );

        date_default_timezone_set ( Load::config ( 'datetime.time_zone' , 'UTC' ) );

        return $this;
    }


    public function callAppKernel()
    {

        if (class_exists ( '\App\Kernel' ))
        {
            $kernel = new \App\Kernel();

            if (property_exists($kernel, 'middleware'))
            {
              foreach ($kernel->middleware as $middleware)
              {
                  Middleware::init($middleware,true);
              }
            }

            if (method_exists($kernel,'boot'))
            {
                $kernel->boot ();
            }
        }

        return $this;
    }



    private function setPublicPath ( )
    {
        if (!defined ( 'PUBLIC_DIR' ))
        {
            if (isset( $_SERVER[ 'SCRIPT_FILENAME' ] ) && !empty( $_SERVER[ 'SCRIPT_FILENAME' ] ))
            {
                $_ = explode ( '/' , $_SERVER[ 'SCRIPT_FILENAME' ] );

                array_pop ( $_ );

                define ( 'PUBLIC_DIR' , implode ( '/' , $_ ) );
            }
            else
            {
                define ( 'PUBLIC_DIR' , BASEPATH . DS . 'public' );
            }
        }
    }



    private function setAliases ()
    {
        $aliases = Load::config ( 'aliases' , [] );

        foreach ($aliases as $key => $value) {
            class_alias ( '\\' . $value , $key );
        }
    }



    public function routing ()
    {
        $routes = null;

        if(file_exists($routes_cache_file = path('storage/system/routes.php'))) {
            $routes = require_once $routes_cache_file;
        } else {
            import_dir_files ( path ( 'routes' ) );
        }

        if(!inConsole()) {
          Route::execute ($routes);
        }

        unset($routes);

        return $this;
    }


    public function response()
    {
        return Load::class('response');
    }


    private function loadHelpers ()
    {
        import_dir_files ( path ( 'app/Helpers' ) );
    }



    private function setPathDefines ()
    {
        define ( 'BASEPATH' , $this->basePath );

        define ( 'DS' , DIRECTORY_SEPARATOR );

        $this->setPublicPath ();

        chdir ( BASEPATH . DS );
    }


    public function benchmark($finish)
    {
      if(InConsole() || !Load::config('app.debug') || Load::class('http')->isAjax()) {
          return null;
      } else {
        Benchmark::show($finish);
      }
    }


}
