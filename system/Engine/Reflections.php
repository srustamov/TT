<?php namespace System\Engine;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


use ReflectionMethod;
use ReflectionFunction;

class Reflections
{
    public static function classMethodParameters($className, $method, array $args = [])
    {
        if (!method_exists($className, $method)) {
            return $args;
        }

        $pParameters = (new ReflectionMethod($className, $method))->getParameters();

        foreach ($pParameters as $num => $param) {
            if ($param->getClass()) {
                if(!$param->isDefaultValueAvailable()) {
                    $args[$num] = App::get($param->getClass()->name);
                }
               
            }
        }
        return $args;
    }


    public static function functionParameters($function, array $args = [])
    {
        $parameters  = (new ReflectionFunction($function))->getParameters();

        foreach ($parameters as $num => $param) {
            if ($param->getClass()) {
                $args[$num] = App::get($param->getClass()->name);
            }
        }
        return $args;
    }
}
