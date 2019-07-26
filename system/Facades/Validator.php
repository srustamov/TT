<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static make($data, array $rules)
 * @method static messages()
 */



class Validator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'validator';
    }
}
