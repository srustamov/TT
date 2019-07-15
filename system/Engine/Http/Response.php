<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use System\Engine\Load;
use System\Facades\File;

class Response
{
    private $content;

    private $headers = [];

    private $statusCode = 200;

    private $protocol = "HTTP/1.1";

    private $statusMessage;

    private $charset = 'UTF-8';

    private $refresh = 0;

    private $messages = [
        100 => 'Continue' ,
        101 => 'Switching Protocols' ,
        102 => 'Processing' ,
        200 => 'OK' ,
        201 => 'Created' ,
        202 => 'Accepted' ,
        203 => 'Non-Authoritative Information' ,
        204 => 'No Content' ,
        205 => 'Reset Content' ,
        206 => 'Partial Content' ,
        207 => 'Multi-status' ,
        208 => 'Already Reported' ,
        300 => 'Multiple Choices' ,
        301 => 'Moved Permanently' ,
        302 => 'Found' ,
        303 => 'See Other' ,
        304 => 'Not Modified' ,
        305 => 'Use Proxy' ,
        306 => 'Switch Proxy' ,
        307 => 'Temporary Redirect' ,
        400 => 'Bad Request' ,
        401 => 'Unauthorized' ,
        402 => 'Payment Required' ,
        403 => 'Forbidden' ,
        404 => 'Not Found' ,
        405 => 'Method Not Allowed' ,
        406 => 'Not Acceptable' ,
        407 => 'Proxy Authentication Required' ,
        408 => 'Request Time-out' ,
        409 => 'Conflict' ,
        410 => 'Gone' ,
        411 => 'Length Required' ,
        412 => 'Precondition Failed' ,
        413 => 'Request Entity Too Large' ,
        414 => 'Request-URI Too Large' ,
        415 => 'Unsupported Media Type' ,
        416 => 'Requested range not satisfiable' ,
        417 => 'Expectation Failed' ,
        418 => 'I\'m a teapot' ,
        422 => 'Unprocessable Entity' ,
        423 => 'Locked' ,
        424 => 'Failed Dependency' ,
        425 => 'Unordered Collection' ,
        426 => 'Upgrade Required' ,
        428 => 'Precondition Required' ,
        429 => 'Too Many Requests' ,
        431 => 'Request Header Fields Too Large' ,
        451 => 'Unavailable For Legal Reasons' ,
        500 => 'Internal Server Error' ,
        501 => 'Not Implemented' ,
        502 => 'Bad Gateway' ,
        503 => 'Service Unavailable' ,
        504 => 'Gateway Time-out' ,
        505 => 'HTTP Version not supported' ,
        506 => 'Variant Also Negotiates' ,
        507 => 'Insufficient Storage' ,
        508 => 'Loop Detected' ,
        511 => 'Network Authentication Required' ,
    ];


    /**
     * Response constructor.
     * @param string $content
     * @param Int $statusCode
     * @param array $headers
     */
    public function __construct($content = '', $statusCode = 200, array $headers = [])
    {
        $this->make($content, (int) $statusCode, $headers);
    }


    /**
     * @param string $content
     * @param Int $statusCode
     * @param array $headers
     * @return $this
     */
    public function make($content, $statusCode = 200, array $headers = [])
    {
        $this->setContent($content);

        $this->headers = $headers;

        $this->setStatusCode((int) $statusCode);

        return $this;
    }

    /**
     * @param Int $code
     * @param null $message
     * @return $this
     */
    public function setStatusCode(Int $code, $message = null)
    {
        if (is_null($message)) {
            $message = $this->messages[ $code ] ?? '';
        }

        $this->statusMessage = $message;

        $this->statusCode = $code;

        return $this;
    }

    /**
     * @param $data
     * @return Response
     */
    public function json($data)
    {
        $this->contentType('application/json');

        $this->setContent(json_encode($data));

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        return $this;
    }


    /**
     * @param String $path
     * @param String $fileName
     * @return Response
     */
    public function download(String $path, String $fileName = null, $disposition = 'attachment')
    {
        $this->header('Content-Disposition', $disposition.';filename='.(!is_null($fileName) ? $fileName : urlencode($fileName)));
        $this->header('Content-Type', 'application/force-download');
        $this->header('Content-Type', 'application/octet-stream');
        $this->header('Content-Type', 'application/download');
        $this->header('Content-Description', 'File Transfer');
        $this->header('Content-Lenght', File::size($path));
        $this->setContent(File::get($path));

        return $this;
    }

    /**
     * @param $contentType
     * @return Response
     */
    public function contentType($contentType)
    {
        return $this->header('Content-Type', $contentType);
    }

    /**
     * @param $name
     * @param $value
     * @param bool $replace
     * @return Response
     */
    public function header($name, $value, $replace = true)
    {
        $this->headers[ $name ] = array( 'value' => $value , 'replace' => $replace );

        return $this;
    }

    /**
     * @param Array $headers
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->headers = [];

        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }

        return $this;
    }

    /**
     * @param String $charset
     * @return $this
     */
    public function charset(String $charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @param $name
     * @return bool|mixed
     */
    public function getHeader($name)
    {
        if ($this->hasHeader($name)) {
            return $this->headers[ $name ];
        } else {
            $headers = headers_list();

            return $headers[ $name ] ?? false;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasHeader($name): Bool
    {
        return array_key_exists($name, $this->headers);
    }

    /**
     * @param $name
     * @return Response
     */
    public function removeHeader($name)
    {
        if ($this->hasHeader($name)) {
            unset($this->headers[ $name ]);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        if ($content instanceof $this) {
            return $this;
        }

        if (is_array($content)) {
            $content = json_encode($content);
        }


        if (null !== $content && !is_string($content) && !is_numeric($content) && !is_callable(array( $content , '__toString' ))) {
            throw new \UnexpectedValueException(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));
        }

        $this->content = (string) $content;

        return $this;
    }

    /**
     * @param $content
     * @return Response
     */
    public function appendContent($content)
    {
        return $this->setContent($this->getContent().$content);
    }

    /**
     * @param $content
     * @return Response
     */
    public function prependContent($content)
    {
        return $this->setContent($content.$this->getContent());
    }

    /**
     * @param Int $refresh
     * @return $this
     */
    public function refresh(Int $refresh)
    {
        $this->refresh = $refresh;

        return $this;
    }

    /**
     * @param String $url
     * @param int $statusCode
     * @param int $refresh
     * @return mixed
     */
    public function redirect(String $url, $statusCode = 302, $refresh = 0)
    {
        return Load::class('redirect')->to($url, $statusCode, $refresh);
    }


    
    /**
     * @return Response
     */
    public function headersSend()
    {
        if (!headers_sent()) {
            foreach ($this->headers as $name => $header) {
                header($name . ":" . $header[ 'value' ], $header[ 'replace' ]);
            }

            header(sprintf("%s %d %s", $this->protocol(), (int)$this->statusCode, $this->statusMessage));
        }

        return $this;
    }

    /**
     * @return Response
     */
    public function send()
    {
        if ($this->refresh > 0) {
            sleep($this->refresh);
        }

        if (!$this->hasHeader('Content-Type')) {
            $this->contentType("text/html;charset={$this->charset}");
        }

        $this->headersSend();

        if (Load::class('request')->isMethod('HEAD')) {
            $this->setContent(null);
        }

        $this->sendContent();

        $this->setContent(null);

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            if (!CONSOLE) {
                static::closeOutputBuffers();
            }
        }

        return $this;
    }


    /**
     * @author Symfony
    */
    public static function closeOutputBuffers()
    {
        $status = ob_get_status(true);

        $level = count($status);

        $flags = defined('PHP_OUTPUT_HANDLER_REMOVABLE') ? PHP_OUTPUT_HANDLER_REMOVABLE | PHP_OUTPUT_HANDLER_FLUSHABLE  : -1;

        while ($level-- > 0 && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || $flags === ($s['flags'] & $flags) : $s['del'])) {
            ob_end_flush();
        }
    }


    /**
     * @param String $protocol
     * @return string|Response
     */
    public function protocol(String $protocol = null)
    {
        if (!is_null($protocol)) {
            $this->protocol = $protocol;

            return $this;
        } else {
            return $_SERVER[ 'SERVER_PROTOCOL' ] ?? $this->protocol;
        }
    }

    /**
     * @return Response
     */
    public function sendContent()
    {
        echo $this->content;

        return $this;
    }


    public function __toString()
    {
        $this->send();
    }
}
