<?php

/**
* @author  Samir Rustamov <rustemovv96@gmail.com>
* @link    https://github.com/srustamov/TT
*/

namespace System\Engine\Cli;

/**
 * Description of Create
 *
 * @author Samir Rustamov
 */


class Create
{
    public static function execute($manage)
    {
        $type= explode(':', $manage[ 0 ], 2)[1];

        if ($type == 'facade') {
            return static::facade($manage[1]);
        }

        $is_resource = false;

        if ($type == 'resource') {
            $is_resource = true;
            $type = 'controller';
        }

        $type = ucfirst($type);

        $_type = $type;

        if (!isset($manage[ 1 ])) {
            new PrintConsole("error", "\nPlease enter {$type} name \n\n");
            exit();
        }

        $name = $manage[ 1 ];

        $namespace = $type == 'Middleware' ? "namespace App\\{$type}" : "namespace App\\{$type}" . "s";

        if (strpos($name, '/')) {
            $_file = explode('/', $manage[ 1 ]);

            $name = array_pop($_file);

            if (count($_file) > 0) {
                $namespace .= '\\' . implode('\\', $_file);
            }
        }


        switch ($type) {
            case 'Controller':
                $type = 'Controllers';
                if ($is_resource) {
                    $write_data = str_replace([ ':namespace' , ':name' ], [ $namespace , $name ], file_get_contents(__DIR__ . '/resource/resource.mask'));
                } else {
                    $write_data = str_replace([ ':namespace' , ':name' ], [ $namespace , $name ], file_get_contents(__DIR__ . '/resource/controller.mask'));
                }
                break;
            case 'Model':
                $write_data = str_replace([ ':namespace' , ':name' ], [ $namespace , $name ], file_get_contents(__DIR__ . '/resource/model.mask'));
                $type = 'Models';
                break;
            case 'Middleware':
                $type = 'Middleware';
                $write_data = str_replace([ ':namespace' , ':name' ], [ $namespace , $name ], file_get_contents(__DIR__ . '/resource/middleware.mask'));
                break;
            default:
                new PrintConsole("error", "\nCreate {$type} name undefained. Please use type ['controller,model,middleware']\n\n");
                exit();
                break;
        }


        if (!file_exists("app/{$type}/" . $manage[ 1 ] . '.php')) {
            $_ = explode('/', $manage[ 1 ]);

            if (\count($_) > 1) {
                array_pop($_);

                if (\count($_) > 1) {
                    $__ = $_;
                    $path = '';
                    foreach ($_ as $dir) {
                        $path .= array_shift($__) . '/';

                        mkdir(app_path($type . '/' . $path, 'app'));
                    }
                } else {
                    mkdir(app_path($type . '/' . implode('/', $_), 'app'));
                }
            }


            if (touch(app_dir("{$type}/{$manage[1]}.php"))) {
                try {
                    file_put_contents(app_path("{$type}/{$manage[1]}.php"), $write_data);

                    new PrintConsole("green", "\nCreate $name {$_type} successfully\n\n");
                } catch (\Exception $e) {
                }
            } else {
                new PrintConsole("error", "\nCreate file failed\n\n");
            }
        } else {
            new PrintConsole("error", "\nThe file was already created\n\n");
        }
    }


    protected static function facade($name)
    {
        if (!file_exists($file = path('system/Facades/'.ucfirst($name).'.php'))) {
            $content = str_replace(
                [':namespace',':name',':accessor'],
                ['namespace System\\Facades',ucfirst($name),strtolower($name)],
                file_get_contents(__DIR__.'/resource/facade.mask')
            );

            $create_and_put = file_put_contents($file, $content);

            if ($create_and_put) {
                new PrintConsole("green", "\nCreate Facade successfully\n\n");
            } else {
                new PrintConsole("error", "\nCreate Facade failed\n\n");
            }
        } else {
            new PrintConsole("error", "\nFacade already exists\n\n");
        }
    }
}
