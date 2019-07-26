<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static size(String $path)
 * @method static get(String $path)
 */



class File extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'file';
    }
}
