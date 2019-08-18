<?php namespace System\Facades;



/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 *
 * @method static addClaim(string $key,$value): self
 * @method static set(string $key,$value): self
 * @method static for(string $permittedFor): self
 * @method static setId($id): self
 * @method static expire(int $seconds): self
 * @method static getToken()
 * @method static parseToken(string $token)
 * @method static make(string $token): self
 * @method static get(string $key,$default =null)
 * @method static validate(): bool
 */



class Jwt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'jwt';
    }
}
