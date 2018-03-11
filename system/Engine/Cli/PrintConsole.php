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
 * Description of PrintConsole
 *
 * @author Samir Rustamov
 */
class PrintConsole {


    private static $support;


    /**
     * PrintConsole constructor.
     */
    public function __construct() {
        if(count(func_get_args()) == 2) {
            $args = func_get_args();
            echo static::_printData($args[0], $args[1]);
        }
    }


    /**
     * @param $style
     * @param $text
     * @return null|string
     */
    private static function _printData( $style , $text )
    {
        if (!InConsole()) {
            return null;
        }
        $styles = [
            'reset' => "\033[0m" ,
            'red' => "\033[31m" ,
            'green' => "\033[32m" ,
            'yellow' => "\033[33m" ,
            'error' => "\033[37;41m" ,
            'success' => "\033[37;42m" ,
            'title' => "\033[34m" ,
           ];

        if (is_null ( static::$support )) {
            if (DIRECTORY_SEPARATOR == '\\') {
                static::$support = false !== getenv ( 'ANSICON' ) || 'ON' === getenv ( 'ConEmuANSI' );
            } else {
                static::$support = function_exists ( 'posix_isatty' ) && posix_isatty ( STDOUT );
            }
        }

        return  ( static::$support ? $styles[ $style ] : '' ) . $text . ( self::$support ? $styles[ 'reset' ] : '' );

    }



    public static function commandList ()
    {
        echo static::_printData("green",

                "runserver [ port(default 8000) ]\n\n".
                "create:controller [Controller Name]\n\n".
                "create:resource [Controller Name]\n\n".
                "create:model [Model Name]\n\n".
                "create:middleware [Middleware Name]\n\n".
                "session:table --create [tableName] (Database Migration Session table) \n\n".
                "users:table (Database Migration users table) \n\n".
                "view:cache (View cache files all clear)\n\n".
                "config:cache (Configs cache file all clear)\n\n".
                "config:cache --create (Configs  files all cache)\n\n".
                "key:generate \n\n"
            );

    }



    public static function output ()
    {
        echo static::_printData( "title" ,
                "----------------------------------------------------\n".
                " OUTPUT\n".
                "----------------------------------------------------\n"
            );
    }


    public static function benchmark($finish)
    {
        return static::_printData('title',
            'Execute Time:'.round($finish - APP_START,4)." seconds\n"
            );
    }

}
