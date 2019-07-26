<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static locale($lang)
 * @method static translate(string $string, array $array)
 */



class Language extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'language';
    }
}
