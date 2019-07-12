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
use System\Engine\App;

class Route
{
    public static function clearRoutesCacheOrCreate($subCommand)
    {
        $file = App::instance()->routesCacheFile();

        if ($subCommand == '--create') {
            $routesArray = BaseRoute::getRoutes();

            file_put_contents($file, "<?php \n\n return array(\n\n");

            static::create($routesArray);

            file_put_contents($file, ");", FILE_APPEND);

            new PrintConsole('green', "\n\nRoutes cached successfully \n\n");
        } else {
            if (file_exists($file)) {
                unlink($file);
            }

            new PrintConsole('green', "\n\nCache routes clear successfully \n\n");
        }
    }

    protected static function create($routesArray)
    {
        $file = App::instance()->routesCacheFile();

        foreach ($routesArray as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    file_put_contents($file,"\t array(\n\n",FILE_APPEND);
                } else {
                    file_put_contents($file,"\t'" . $key . "' => array(\n\n",FILE_APPEND);
                }

                static::create($value);

                file_put_contents($file,"\t),\n\n",FILE_APPEND);
            } else {
                if (is_bool($value)) {
                    $value = $value ? "true" : "false";
                } elseif (\is_integer($value)) {
                } else {
                    $value = "'$value'";
                }

                if (is_numeric($key)) {
                    file_put_contents($file, "\t$value, \n\n", FILE_APPEND);
                } else {
                    file_put_contents($file, "\t'" . $key . "' => $value, \n\n", FILE_APPEND);
                }
            }
        }
    }

    public static function list()
    {
        $output = "\n";

        $file = App::instance()->routesCacheFile();

        if (file_exists($file)) {
            $routes = require $file;
        } else {
            $routes = BaseRoute::getRoutes();
        }


        $mask = file_get_contents(__DIR__.'/resource/routelist.mask');

        foreach ($routes as $method => $parameters) {
            foreach ($parameters as $key => $param) {
                $output .= str_replace(
                    array(':METHOD',':AJAX',':URL',':HANDLER',':MIDDLEWARE',':PATTERN'),
                    array(
                            $method,
                            $param['ajax'] ? 'ajax' : '',
                            $param['path'],
                            $param['handler'],
                            implode(',', $param['middleware']),
                            implode($param['pattern'])
                        ),
                    $mask
                    )."\n";
            }
        }


        new PrintConsole('reset', $output);
    }
}
