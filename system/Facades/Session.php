<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static delete(string $string)
 * @method static destroy()
 * @method static set(string $string, bool $true)
 * @method static get(string $string)
 */


class Session extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'session';
    }
}
