<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static random(int $int)
 * @method static encrypt($data)
 * @method static decrypt($data)
 */



class OpenSsl extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'openssl';
    }
}
