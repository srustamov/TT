<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

use System\Engine\Cli\PrintConsole;

class Create {


    public static function execute ( $manage )
    {

        $type= explode ( ':' , $manage[ 0 ] , 2 )[1];


        $is_resource = false;

        if ($type == 'resource') {
            $is_resource = true;
            $type = 'controller';
        }

        $type = ucfirst ( $type );

        $_type = $type;

        if (!isset( $manage[ 1 ] )) {
            new PrintConsole ( "error" , "\nPlease enter {$type} name \n\n" );
            exit();
        }

        $name = $manage[ 1 ];

        $namespace = $type == 'Middleware' ? "namespace App\\{$type}" : "namespace App\\{$type}" . "s";

        if (strpos ( $name , '/' )) {
            $_file = explode ( '/' , $manage[ 1 ] );

            $name = array_pop ( $_file );

            if (count ( $_file ) > 0) {
                $namespace .= '\\' . implode ( '\\' , $_file );
            }
        }


        switch ($type) {
            case 'Controller':
                $type = 'Controllers';
                if ($is_resource) {
                    $write_data = str_replace ( [ ':namespace' , ':name' ] , [ $namespace , $name ] , file_get_contents ( __DIR__ . '/resource/resource.mask' ) );
                } else {
                    $write_data = str_replace ( [ ':namespace' , ':name' ] , [ $namespace , $name ] , file_get_contents ( __DIR__ . '/resource/controller.mask' ) );
                }
                break;
            case 'Model':
                $write_data = str_replace ( [ ':namespace' , ':name' ] , [ $namespace , $name ] , file_get_contents ( __DIR__ . '/resource/model.mask' ) );
                $type = 'Models';
                break;
            case 'Middleware':
                $type = 'Middleware';
                $write_data = str_replace ( [ ':namespace' , ':name' ] , [ $namespace , $name ] , file_get_contents ( __DIR__ . '/resource/middleware.mask' ) );
                break;
            default:
                new PrintConsole ( "error" , "\nCreate {$type} name undefained. Please use type ['controller,model,middleware']\n\n" );
                exit();
                break;
        }


        if (!file_exists ( "app/{$type}/" . $manage[ 1 ] . '.php' )) {


            $_ = explode ( '/' , $manage[ 1 ] );

            if (\count ( $_ ) > 1) {
                array_pop ( $_ );

                if (\count( $_ ) > 1) {
                    $__ = $_;
                    $path = '';
                    foreach ($_ as $dir) {
                        $path .= array_shift ( $__ ) . '/';

                        @mkdir ( path ( $type . '/' . $path , 'app' ) );
                    }
                } else {
                    @mkdir ( path ( $type . '/' . implode ( '/' , $_ ) , 'app' ) );
                }

            }

            $file = @touch ( "app/{$type}/{$manage[1]}.php" );

            if ($file) {
                try {
                    file_put_contents ( "app/{$type}/$manage[1].php" , $write_data );

                    new PrintConsole ( "success" , "\nCreate $name {$_type} successfully\n\n" );
                } catch (\Exception $e) {
                }
            } else {
                new PrintConsole ( "success" , "\nCreate file failed\n\n" );
            }

        } else {
            new PrintConsole ( "error" , "\nThe file was already created\n\n" );
        }
    }

}
