<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 *
 * @method static guest(): bool
 * @method static check(): bool
 * @method static attempt(array $data, $remember = false): bool
 * @method static logoutUser()
 * @method static getMessage()
 * @method static user()
 */



class Auth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'authentication';
    }
}
