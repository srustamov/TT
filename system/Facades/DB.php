<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static table($table): self
 * @method static where(): self
 * @method static first()
 * @method static get()
 * @method static exec(string $query)
 */


class DB extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'database';
    }
}
