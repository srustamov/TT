<?php

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
class PrintConsole
{
    private static $support;


    /**
     * PrintConsole constructor.
     */
    public function __construct()
    {
        if (count(func_get_args()) == 2) {
            $args = func_get_args();
            
            echo static::_printData($args[0], $args[1]);
        }
    }




    /**
     * @param $style
     * @param $text
     * @return null|string
     */
    private static function _printData($style, $text)
    {
        if (!CONSOLE) {
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

        if (is_null(static::$support)) {
            if (DIRECTORY_SEPARATOR == '\\') {
                static::$support = false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
            } else {
                static::$support = function_exists('posix_isatty') && posix_isatty(STDOUT);
            }
        }

        return  (static::$support ? $styles[ $style ] : '') . $text . (self::$support ? $styles[ 'reset' ] : '');
    }



    public static function commandList()
    {
        echo static::_printData("yellow", file_get_contents(__DIR__.'/resource/commands.mask'));
    }



    public static function output()
    {
        // echo static::_printData( "title" ,
        //         "----------------------------------------------------\n".
        //         " OUTPUT\n".
        //         "----------------------------------------------------\n"
        //     );
    }


    public static function benchmark($finish)
    {
        return static::_printData(
            'title',
            'Execute Time:'.round($finish - APP_START, 4)." seconds\n"
            );
    }
}
