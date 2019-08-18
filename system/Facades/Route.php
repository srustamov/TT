<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static execute(\System\Engine\App $param, array $routeMiddleware)
 * @method static getRoutes()
 * @method static getName($name, array $parameters)
 * @method static get($path, $handler): self
 * @method static post($path, $handler): self
 * @method static put($path, $handler): self
 * @method static delete($path, $handler): self
 * @method static options($path, $handler): self
 * @method static patch($path, $handler): self
 * @method static form($path, $handler): self
 * @method static any($path, $handler): self
 * @method static $group_parameters, \Closure $callback($path, $handler): self
 * @method static group($group_parameters, \Closure $callback)
 * @method static name($name): self
 * @method static pattern($pattern): self
 */




class Route extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'route';
    }
}
