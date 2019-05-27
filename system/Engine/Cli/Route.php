<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


/**
* @author  Samir Rustamov <rustemovv96@gmail.com>
* @link    https://github.com/srustamov/TT
*/

namespace System\Engine\Cli;

/**
 * Description of Config
 *
 * @author Samir Rustamov
 */


use System\Facades\Route as BaseRoute;

class Route {


    public static function clearRoutesCacheOrCreate($subCommand)
    {
        if ($subCommand == '--create')
        {
            $routesArray = BaseRoute::getRoutes();

            $__file = path ( 'storage/system/routes.php' );

            file_put_contents ( $__file , "<?php \n\n return array(\n\n" );

            static::create ( $routesArray );

            file_put_contents ( $__file , ");" , FILE_APPEND );

            new PrintConsole ( 'green' , "\n\nRoutes cached successfully \n\n" );
        }
        else
        {
            if(file_exists(path ( 'storage/system/routes.php' )))
            {
                unlink ( path ( 'storage/system/routes.php' ) );
            }

            new PrintConsole ( 'green' , "\n\nCache routes clear successfully \n\n" );
        }
    }

    protected static function create ( $routesArray )
    {

        foreach ($routesArray as $key => $value)
        {

            if (is_array ( $value ))
            {
                if(is_numeric($key))
                {
                  file_put_contents ( path ( 'storage/system/routes.php' ) ,
                      "\t array(\n\n" , FILE_APPEND );
                }
                else
                {
                  file_put_contents ( path ( 'storage/system/routes.php' ) ,
                      "\t'" . $key . "' => array(\n\n" , FILE_APPEND );
                }

                static::create ( $value );

                file_put_contents ( path ( 'storage/system/routes.php' ) ,
                    "\t),\n\n" , FILE_APPEND );

            }
            else
            {
              if (is_bool( $value ))
              {
                  $value = $value ? "true" : "false";
              }
              elseif (\is_integer( $value ))
              {}
              else
              {
                  $value = "'$value'";
              }

              if (is_numeric ( $key ))
              {
                  file_put_contents ( path ( 'storage/system/routes.php' ) , "\t$value, \n\n" , FILE_APPEND );
              }
              else
              {
                  file_put_contents ( path ( 'storage/system/routes.php' ) , "\t'" . $key . "' => $value, \n\n" , FILE_APPEND );
              }
            }


        }
    }

    public static function list()
    {
        $output = "\n";

        if (file_exists(path('storage/system/routes.php')))
        {
            $routes = require path('storage/system/routes.php');
        }
        else
        {
            $routes = BaseRoute::getRoutes();
        }


        $mask = file_get_contents(__DIR__.'/resource/routelist.mask');

        foreach ($routes as $method => $parameters )
        {
            foreach ($parameters as $key => $param)
            {
                $output .= str_replace(
                        array(':METHOD',':AJAX',':URL',':HANDLER',':MIDDLEWARE',':PATTERN'),
                        array(
                            $method,
                            $param['ajax'] ? 'ajax' : '',
                            $param['path'],
                            $param['handler'],
                            implode(',',$param['middleware']),
                            implode($param['pattern'])
                        ),
                        $mask
                    )."\n";
            }
        }


        new PrintConsole ( 'reset' , $output);

    }
}
