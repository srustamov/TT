<?php namespace System\Facades;

use Exception;
use RuntimeException;
use System\Engine\Load;

abstract class Facade
{
    /**
     *
     */
    protected static function getFacadeAccessor()
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }


    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($method, $args)
    {
        $instance = Load::class(static::getFacadeAccessor());

        return $instance->$method(...$args);
    }
}
