<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @method static route($name, array $parameters = []): self
 * @method static back($refresh = 0, $http_response_code = 302): self
 * @method static instance(): self
 * @method static with($key, $value = null): self
 * @method static withErrors($key, $value = null)
 * @method static to(String $url, $refresh = 0, $http_response_code = 302): self
 */



class Redirect extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'redirect';
    }
}
