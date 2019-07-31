<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/tt
 *
 * @method static set($key,$value = null)
 * @method static get($key,$default = null)
 * @method static push()
 * @method static prepend()
 * @method static delete()
 * @method static forget()
 * @method static has()
 */

class Config extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'config';
    }
}
