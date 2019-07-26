<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 */

namespace System\Facades;

/**
 * @method static send()
 * @method static make()
 * @method static setStatusCode()
 * @method static setContent()
 * @method static header()
 * @method static withHeaders()
 * @method static download()
 * @method static charset()
 * @method static getHeader()
 * @method static hasHeader()
 * @method static removeHeader()
 * @method static getContent()
 * @method static appendContent()
 * @method static prependContent()
 * @method static refresh()
 * @method static redirect()
 * @method static headersSend()
 * @method static closeOutputBuffers()
 * @method static protocol()
 * @method static sendContent()
 * @method static json($data = null,$statusCode = null)
 */
class Response extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'response';
    }
}
