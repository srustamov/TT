<?php

/**
* @author  Samir Rustamov <rustemovv96@gmail.com>
* @link    https://github.com/srustamov/TT
*/

namespace System\Engine\Cli;

class Create
{
    public static function execute($argv)
    {
        $subCommand = null;
        if(version_compare(PHP_VERSION, '7.3.0') >= 0) {
            //list($command, $name, &$subCommand) = $argv;
        } else {
            @list($command, $name, $subCommand) = $argv;
        }

        list($create, $type) = explode(':', $command, 2);

        if (strlen($type) === 1) {
            $replace = [
            'c' => 'controller',
            'm' => 'model',
            'f' => 'facade',
            'r' => 'resource',
          ];
            $type = $replace[strtolower($type)];
        }

        if (!isset($argv[1])) {
            new PrintConsole('error', "\nPlease enter {$type} name \n\n");
            exit();
        }


        if ($type === 'facade') {
            self::facade($argv[1]);
            return;
        }

        $type = ucfirst($type);

        $namespace = ($type === 'Middleware') ? "namespace App\\{$type}" : "namespace App\\{$type}" . "s";

        if (strpos($name, '/')) {
            $part = explode('/', $name);
            $name = array_pop($part);
            $dir  = implode('/', $part);
            $namespace .= '\\' . str_replace('/', '\\', $dir);
        }


        switch ($type) {
            case 'Controller':
            case 'Model':
            case 'Middleware':
                if ($subCommand && strtolower($subCommand) === '-r') {
                    $content = str_replace(
                        [ ':namespace' , ':name' ],
                        [ $namespace , $name ],
                        file_get_contents(__DIR__ . '/resource/resource.mask')
                     );
                } else {
                    $content = str_replace(
                        [ ':namespace' , ':name' ],
                        [ $namespace , $name ],
                        file_get_contents(__DIR__ . '/resource/'.\strtolower($type).'.mask')
                    );
                }
                $type .= ($type == 'Middleware') ?'':'s';
                break;
            default:
                new PrintConsole('error', "\nCreate {$type} name undefined. Please use type ['controller,model,middleware']\n\n");
                exit();
        }

        if (!file_exists("app/{$type}/{$argv[1]}.php")) {
            if (isset($dir) && !is_dir(app_path($type.'/'.$dir))) {
                if (!mkdir(\app_path($type.'/'.$dir), 0777, true)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', app_path($dir)));
                }
            }
            if (touch(app_path("{$type}/{$argv[1]}.php"))) {
                file_put_contents(app_path("{$type}/{$argv[1]}.php"), $content);
                new PrintConsole('green', "\nCreate $name successfully\n\n");
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
