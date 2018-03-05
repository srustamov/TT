<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 */


class Validator
{
    public static function __callStatic($method, $args)
    {
        return  (new \System\Libraries\Validator())->{$method}(...$args);
    }
}
