<?php namespace System\Facades;
/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 */

use System\Facades\Facade;

class Arr extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'array';
    }
}
