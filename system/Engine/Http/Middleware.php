<?php  namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


use RuntimeException;
use System\Engine\Load;

class Middleware
{
    private static $instance;


    /**
     * @param String $extension
     * @param Bool $isClassName
     * @return bool|mixed
     * @throws \Exception
     */

    public static function init(String $extension, Bool $isClassName = false)
    {
        $request  = Load::class('request');

        if (!$isClassName) {
            list($name, $excepts) = static::instance()->getExcepts($extension);

            foreach ($excepts as $action) {
                if ($request->controller(true) === strtolower($action)) {
                    return true;
                }
            }

            $middleware = "\\App\\Middleware\\{$name}";
        } else {
            $middleware = $extension;
        }

        $next = static function ($ClientRequest) {
            if (Load::isInstance($ClientRequest, 'request')) {
                return Load::class('response');
            }
        };


        if (class_exists($middleware)) {
            $response = call_user_func([new $middleware(), 'handle'], $request, $next);

            if (!Load::isInstance($response, 'response')) {
                Load::class('response')->setContent($response)->send();
            }
        } else {
            throw new RuntimeException("Middleware {$middleware} class not found");
        }
    }


    /**
     * @param $extension
     * @return array
     */
    protected function getExcepts($extension): array
    {
        $excepts = [];

        $name    = $extension;

        if (strpos($extension, '|') !== false) {
            list($name, $excepts) = explode('|', $extension, 2);

            $excepts = explode(',', $excepts);
        }


        return array($name,$excepts);
    }



    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
