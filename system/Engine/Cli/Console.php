<?php namespace System\Engine\Cli;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use System\Engine\App;
use System\Engine\Cli\Route as CliRoute;
use System\Engine\LoadEnvVariables;

class Console
{


    /**
     * @param $command
     * @param bool $shell
     * @return null
     */
    public static function command($command, $shell = false)
    {
        if ($shell === true) {
            return shell_exec($command);
        }

        if (!is_array($command)) {
            $command = explode(' ', $command);
        }

        static::run(array_merge([ 'manage' ], array_filter($command)));
    }


    /**
     * @param array $argv
     */
    public static function run(array $argv)
    {
        $instance = new static();


        if (isset($argv[ 1 ])) {
            $manage = array_slice($argv, '1');
        } else {
            return PrintConsole::commandList();
        }

        PrintConsole::output();

        switch (strtolower($manage[ 0 ])) {
            case 'runserver':
              case 'serve':
              case 'start':
              case 'run':
                $instance->startPhpDevelopmentServer($manage);
                break;
            case 'session:table':
                CreateTables::session($manage);
                break;
            case 'users:table':
                CreateTables::users();
                break;
            case 'view:cache':
                $instance->clearViewCache();
                break;
            case 'config:cache':
                Config::clearConfigsCacheOrCreate($manage[ 1 ] ?? null);
                break;
            case 'route:cache':
                CliRoute::clearRoutesCacheOrCreate($manage[ 1 ] ?? null);
                break;
            case 'route:list':
                CliRoute::list();
                break;
            case 'key:generate':
                $instance->keyGenerate();
                break;
            case 'build':
            case 'prod':
            case 'production':
                self::appDebugFalse();
                $instance->keyGenerate();
                (new LoadEnvVariables(App::getInstance()))->handle();
                self::command('config:cache --create');
                self::command('route:cache --create');
                new PrintConsole('success', PHP_EOL.'Getting Application in Production :)'.PHP_EOL.PHP_EOL);
                break;
            case 'create:controller':
            case 'create:model':
            case 'create:middleware':
            case 'create:resource':
            case 'create:facade':
            case 'c:middleware':
            case 'c:c':
            case 'c:m':
            case 'c:r':
            case 'c:f':
                Create::execute($manage);
                break;
            default:
                PrintConsole::commandList();
                break;
        }
    }



    protected function startPhpDevelopmentServer(array $manage)
    {
        if (isset($manage[ 1 ]) && is_numeric($manage[ 1 ])) {
            $port = $manage[ 1 ];
        } else {
            $port = 8000;
        }

        new PrintConsole('green', "\nPhp Server Run <http://localhost:$port>\n");

        exec('php -S localhost:' . $port . ' -t public/');
    }




    protected function clearViewCache()
    {
        foreach (glob(path('storage/cache/views/*')) as $file) {
            if (is_file($file)) {
                if (unlink($file)) {
                    echo "Delete: [{$file}]\n";
                } else {
                    new PrintConsole('error', 'Delete failed:[' . $file . ']');
                }
            }
        }
        new PrintConsole('green', "\n\nCache files clear successfully \n\n");
    }


    protected function keyGenerate()
    {
        $app = App::get('app');

        $envFile = $app->envFile();

        try {
            $file = fopen($envFile, 'rb+');

            while (($line = fgets($file, 4096)) !== false) {
                if (strpos(trim($line), 'APP_KEY') === 0) {
                    $replace = $line;
                    break;
                }
            }

            fclose($file);

            $content = \file_get_contents($envFile);

            $key = base64_encode(openssl_random_pseudo_bytes(40));

            $key = 'APP_KEY = ' . str_replace('=', '', $key) . "\n";

            if (isset($replace)) {
                $new_content = \preg_replace("/{$replace}/", $key, $content);
                file_put_contents($envFile, $new_content);
            } else {
                file_put_contents($envFile, $key.FILE_APPEND);
            }



            if (file_exists($app->envCacheFile())) {
                unlink($app->envCacheFile());
            }

            new PrintConsole('green', $key);
        } catch (\Exception $e) {
            new PrintConsole('error', $e->getMessage() . "\n");
        }
    }


    private static function appDebugFalse()
    {
        $app = App::get('app');

        $envFile = $app->envFile();

        try {
            $file = fopen($envFile, 'rb+');

            while (($line = fgets($file, 4096)) !== false) {
                if (strpos(trim($line), 'APP_DEBUG') === 0) {
                    $replace = $line;
                    break;
                }
            }

            fclose($file);


            $content = \file_get_contents($envFile);


            $key = 'APP_DEBUG = ' . str_replace('=', '', 'FALSE') . "\n";

            if (isset($replace)) {
                $new_content = \preg_replace("/{$replace}/", $key, $content);
                file_put_contents($envFile, $new_content);
            } else {
                file_put_contents($envFile, $key.FILE_APPEND);
            }
        } catch (\Exception $e) {
            new PrintConsole('error', $e->getMessage() . "\n");
        }
    }
}
