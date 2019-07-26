<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static get(string $string)
 * @method static set(string $string, $session, $get)
 * @method static has(string $string)
 * @method static forget(string $string)
 */



class Cookie extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cookie';
    }
}
