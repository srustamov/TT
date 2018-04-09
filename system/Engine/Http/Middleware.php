<?php  namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


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

    public static function init( String $extension ,Bool $isClassName = false)
    {
        $request  = Load::class('request');

        if(!$isClassName)
        {
            list($name,$guard,$excepts) = static::instance()->getExceptsAndGuard($extension);

            foreach ($excepts as $action)
            {
                if ($request->controller(true) == strtolower($action))
                {
                    return true;
                }
            }

            $middleware = "\\App\\Middleware\\{$name}";
        }
        else
        {
            $middleware = $extension;
            $guard      = null;
        }

        $next = function($ClientRequest)
        {
            if(Load::isInstance($ClientRequest,'request'))
            {
              return Load::class('response');
            }
        };


        if (class_exists($middleware))
        {
            $response = call_user_func_array([ new $middleware() , "handle" ],array($request ,$next,$guard));

            if(!Load::isInstance($response,'response'))
            {
              Load::class('response')->setContent($response)->send();
            }
        }
        else
        {
            throw new \Exception("Middleware {$middleware} class not found");
        }

    }


    /**
     * @param $extension
     * @return array
     */
    protected function getExceptsAndGuard( $extension)
    {
        $excepts = [];

        $guard   = 'user';

        $name    = $extension;

        if (strpos($extension, ':') !== false)
        {
            list($name, $guard) = explode(':', $name, 2);

            if(strpos($guard,'|') !== false)
            {
                list($guard,$excepts) = explode('|',$guard,2);

                $excepts = explode(',',$excepts);
            }

        }
        elseif (strpos($extension,'|') !== false)
        {
            list($name,$excepts) = explode('|',$extension,2);

            $excepts = explode(',',$excepts);
        }


        return array($name,$guard,$excepts);

    }



    public static function instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }






}
