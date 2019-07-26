<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static render(String $file, array $data)
 */



class View extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'view';
    }
}
