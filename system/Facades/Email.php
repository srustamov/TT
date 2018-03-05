<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 */



class Email
{
    public static function __callStatic($method, $args)
    {
        return (new \System\Libraries\Mail\Email())->{$method}(...$args);
    }
}
