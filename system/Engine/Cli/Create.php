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
    public static function execute($argv)
    {
        $type= strtolower(explode(':', $argv[ 0 ], 2)[1]);

        if ($type === 'facade') {
            self::facade($argv[1]);

            return;
        }

        $is_resource = false;

        if ($type === 'resource') {
            $is_resource = true;
            $type = 'controller';
        }

        $type = ucfirst($type);

        $_type = $type;

        if (!isset($argv[ 1 ])) {
            new PrintConsole('error', "\nPlease enter {$type} name \n\n");
            exit();
        }

        $name = $argv[ 1 ];

        $namespace = ( $type === 'Middleware') ? "namespace App\\{$type}" : "namespace App\\{$type}" . "s";

        if (strpos($name, '/')) {
            $_file = explode('/', $argv[ 1 ]);

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
                new PrintConsole('error', "\nCreate {$type} name undefined. Please use type ['controller,model,middleware']\n\n");
                exit();
                break;
        }


        if (!file_exists("app/{$type}/" . $argv[ 1 ] . '.php')) {
            $_ = explode('/', $argv[ 1 ]);

            if (\count($_) > 1) {
                array_pop($_);

                if (\count($_) > 1) {
                    $__ = $_;
                    $path = '';
                    foreach ($_ as $dir) {
                        $path .= array_shift($__) . '/';

                        if (!mkdir($concurrentDirectory = app_path($type . '/' . $path)) && !is_dir($concurrentDirectory)) {
                            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                        }
                    }
                } else if (!mkdir($concurrentDirectory = app_path($type . '/' . implode('/', $_))) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }


            if (touch(app_path("{$type}/{$argv[1]}.php"))) {
                try {
                    file_put_contents(app_path("{$type}/{$argv[1]}.php"), $write_data);

                    new PrintConsole('green', "\nCreate $name {$_type} successfully\n\n");
                } catch (\Exception $e) {
                }
            } else {
                new PrintConsole('error', "\nCreate file failed\n\n");
            }
        } else {
            new PrintConsole('error', "\nThe file was already created\n\n");
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
                new PrintConsole('green', "\nCreate Facade successfully\n\n");
            } else {
                new PrintConsole('error', "\nCreate Facade failed\n\n");
            }
        } else {
            new PrintConsole('error', "\nFacade already exists\n\n");
        }
    }
}
