<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static execute(\System\Engine\App $param, array $routeMiddleware)
 * @method static getRoutes()
 * @method static getName($name, array $parameters)
 */




class Route extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'route';
    }
}
