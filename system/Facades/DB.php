<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static table($table): DB
 * @method static where(): DB
 * @method static first()
 * @method static get()
 */


class DB extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'database';
    }
}
