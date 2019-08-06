<?php namespace System\Facades;

/**
 * @package	TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 */

namespace System\Facades;

/**
 * @method static send()
 * @method static ($content, $statusCode = 200, array $headers = []): self
 * @method static setStatusCode(Int $code, $message = null): self
 * @method static contentType($contentType): self
 * @method static header($name, $value, $replace = true): self
 * @method static withHeaders(array $headers): self
 * @method static setHeaders(array $headers): self
 * @method static download(String $path, String $fileName = null, $disposition = 'attachment'): self
 * @method static charset(String $charset): self
 * @method static getHeader($name)
 * @method static hasHeader($name): bool
 * @method static removeHeader()
 * @method static getContent()
 * @method static setContent($content): self
 * @method static appendContent($content): self
 * @method static prependContent($content): self
 * @method static refresh(int $refresh): self
 * @method static redirect(String $url, $refresh = 0, $statusCode = 302): self
 * @method static headersSend(): self
 * @method static closeOutputBuffers()
 * @method static protocol(String $protocol = null)
 * @method static sendContent()
 * @method static json($data = null,$statusCode = null): self
 */
class Response extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'response';
    }
}
