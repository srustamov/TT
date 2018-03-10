<?php namespace System\Engine\Cli;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use System\Engine\Cli\PrintConsole;
use System\Engine\Cli\Config;
use System\Engine\Cli\CreateTables;


class Console
{


    private static $instance;


    /**
     * @param $command
     * @param bool $shell
     * @return null
     */
    public static function command ( $command , $shell = false )
    {
        if ($shell == true) {
            return shell_exec ( $command );
        }

        $_command = \explode( ' ' , str_replace ( '  ' , ' ' , $command ) );

        static::run ( array_merge ( [ 'manage' ] , $_command ) );

    }


    /**
     * @param array $argv
     */
    public static function run ( array $argv )
    {
        static::$instance = new static();


        if (isset( $argv[ 1 ] )) {
            $manage = array_slice ( $argv , '1' );
        } else {
            return PrintConsole::commandList ();
        }

        PrintConsole::output ();

        switch (strtolower ( $manage[ 0 ] )) {
            case 'runserver':
                self::$instance->startPhpDevelopmentServer ( $manage );
                break;
            case 'session:table':
                CreateTables::session ( $manage );
                break;
            case 'users:table':
                CreateTables::users ();
                break;
            case 'view:cache':
                self::$instance->clearViewCache ();
                break;
            case 'config:cache':
                Config::clearConfigsCacheOrCreate ( $manage[ 1 ] ?? null );
                break;
            case 'key:generate':
                self::$instance->keyGenerate ();
                break;
            default:
                $create = array(
                    "create:controller",
                    "create:model",
                    "create:middleware",
                    "create:resource"
                );
                if(in_array($manage[ 0 ], $create)) {
                    Create::execute($manage);
                } else {
                    PrintConsole::commandList ();
                }

                break;
        }
    }



    protected function startPhpDevelopmentServer ( array $manage )
    {
        if(isset( $manage[ 1 ] ) && is_numeric ( $manage[ 1 ] )) {
            $port = $manage[ 1 ];
        } else {
            $port = "8000";
        }

        new PrintConsole ( 'green' ,"\nPhp Server Run <http://localhost:$port>\n" );

        exec ( 'php -S localhost:' . $port . ' -t public/' );

    }




    protected function clearViewCache ()
    {

        foreach (glob ( path ( 'storage/cache/views/*' ) ) as $file) {
            if (is_file ( $file )) {
                if (@\unlink ( $file )) {
                    echo "Delete: [{$file}]\n";
                } else {
                    new PrintConsole ( 'error' , 'Delete failed:[' . $file . ']' );
                }

            }
        }
        new PrintConsole ( 'green' , "\n\nCache clear successfully \n\n" );

    }


    protected function keyGenerate ()
    {
        $settings_file = path ( '.settings' );

        try {
            $file = fopen ( $settings_file , 'r+' );

            while (( $line = fgets ( $file , 4096 ) ) !== false) {
                if (strpos ( trim($line) , 'APP_KEY' ) === 0) {
                    $replace = $line;
                    break;
                }
            }
            fclose ( $file );


            $content = \file_get_contents( $settings_file );

            $key = base64_encode ( openssl_random_pseudo_bytes ( 40 ) );

            $key = "APP_KEY = " . str_replace ( '=' , '' , $key ) . "\n";

            $new_content = \preg_replace( "/{$replace}/" , $key , $content );

            file_put_contents ( path ( '.settings' ) , $new_content );

            new PrintConsole ( 'green' , $key );

        } catch (\Exception $e) {
            new PrintConsole ( 'error' , $e->getMessage () . "\n" );
        }

    }




}
