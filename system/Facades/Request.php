<?php namespace System\Facades;
/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 */

use System\Facades\Facade;

class Request extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'request';
    }
}
