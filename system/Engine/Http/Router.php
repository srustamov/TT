<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */


use System\Facades\Http;
use System\Libraries\Url;
use System\Libraries\Request;
use System\Engine\Http\Middleware;


class Router
{

    protected static $routes = [
        'GET' => [] ,
        'POST' => [] ,
        'PUT' => [] ,
        'DELETE' => [] ,
        'OPTIONS' => [] ,
        'PATCH' => []
    ];


    protected static $count = 0;

    protected static $prefix = '';

    protected static $method = '';

    protected static $path = '';

    protected static $middleware = [];

    protected static $isAjax = false;



    public static function any ( array $methods , $path , $handler )
    {
        $methods = array_map ( function ( $method ) {
            return strtoupper ( $method );
        } , $methods );

        foreach ($methods as $method) {
            self::addRoute ( $method , $path , $handler );
        }
        return ( new static );
    }

    /**
     * @param $method
     * @param $path
     * @param $handler
     * @internal param array $pattern
     * @internal param array $middleware
     */
    protected static function addRoute ( $method , $path , $handler )
    {

        static::$method = $method;
        static::$path = $path;

        array_push ( static::$routes[ $method ] , [
            'path' => static::$prefix . strtolower ( $path ) ,
            'handler' => $handler ,
            'name' => null ,
            'isAjax' => static::$isAjax,
            'middlewares' => self::$middleware
        ] );
    }



    public static function form ( $path , $handler )
    {
        foreach ([ 'GET' , 'POST' ] as $method) {
            self::addRoute ( $method , $path , $handler );
        }
        return ( new static );
    }

    /**
     * @param $handler
     */
    public static function ajax ( $handler )
    {
        static::$isAjax = true;
        call_user_func ( $handler , new Router );
        static::$isAjax = false;
    }



    public static function prefix ( $prefix )
    {
        static::$prefix = $prefix;
        return ( new static );
    }

    /**
     * @param $prefix
     * @param $callback
     */
    public static function group ( $prefix , $callback = null )
    {

        $_prefix = $prefix;

        $middleware = false;

        if (is_null ( $callback )) {
            $callback = $prefix;

            $_prefix = static::$prefix;
        }

        if (is_array ( $prefix )) {
            if (isset( $prefix[ 'prefix' ] )) {
                $_prefix = $prefix[ 'prefix' ];
            }
            if (isset( $prefix[ 'middleware' ] )) {
                self::$middleware[] = $prefix[ 'middleware' ];
                $middleware = true;
            }
        }

        $requestUri = self::getrequestUri ();

        $prefixUri = strtolower ( static::$prefix . $_prefix );

        if ($prefixUri == substr($requestUri,0 ,strlen(static::$prefix.$_prefix))) {

            static::$prefix .= $_prefix;

            call_user_func ( $callback , new Router );

            if ($middleware && isset(self::$middleware[count(self::$middleware)-1]))
            {
                unset(self::$middleware[count(self::$middleware)-1]);
                $middleware = false;
            }
            static::$prefix = substr ( static::$prefix , 0 ,strlen ( static::$prefix ) - strlen ( $_prefix ) );
        }


    }



    protected static function getRequestUri (): String
    {
        return strtolower ( ( new Url() )->request () );
    }



    public static function init ()
    {
        static::match ();
        if (static::$count > 0) {
            return abort ( 404 );
        }
    }

    /**
     * @return bool|int|mixed|void
     */
    protected static function match ()
    {

        $request      = new Request();

        $requestUri   = self::getRequestUri ();

        $method = self::getRequestMethod ();

        $isAjax = $request->ajax();

        $requestParam = [];

        if (isset( static::$routes[ $method ] ))
        {
            foreach (static::$routes[ $method ] as $resource)
            {
                if ($resource[ 'isAjax' ] == true)
                {
                    if (!$isAjax) continue;
                }
                $args = [];
                $route = $resource[ 'path' ] != '/'
                    ? rtrim ( $resource[ 'path' ] , '/' )
                    : $resource[ 'path' ];

                $handler = $resource[ 'handler' ];

                if (preg_match ( '/({.+?})/' , $route ))
                {
                    list( $args , $uri , $route , $requestParam ) = static::parseRoute ( $requestUri , $route , $resource[ 'pattern' ] ?? [] );
                }

                if (!preg_match ( "#^$route$#" , $requestUri ))
                {
                    unset( static::$routes[ $method ] );
                    continue;
                }

                if (!empty( $requestParam ))
                {
                    foreach ($requestParam as $key => $value)
                    {
                        $_REQUEST[ $key ] = $value;
                    }
                }


                if (is_string ( $handler ) && strpos ( $handler , '@' ))
                {
                    list( $controller , $method ) = explode ( '@' , $handler );

                    if (strpos ( $controller , '/' ) !== false)
                    {
                        $controller = str_replace ( '/' , '\\' , $controller );
                    }
                    $controller = "\\App\\Controllers\\$controller";

                    if ($request->method () == 'POST')
                    {
                        $args[] = $request;
                    }

                    if (method_exists ( $controller , $method ))
                    {
                        $_SERVER[ 'CALLED_METHOD' ] = $method;

                        $_SERVER[ 'CALLED_CONTROLLER' ] = substr ( $controller , 17 );

                        if (!empty( $resource[ 'middlewares' ] ))
                        {
                            foreach ($resource[ 'middlewares' ] as $middleware)
                            {
                                Middleware::init ( $middleware );
                            }
                        }
                        return call_user_func_array ( [ ( new $controller() ) , $method ] , $args );
                    }
                    return abort ( 404 );
                }

                if (!empty( $resource[ 'middlewares' ] ))
                {
                    foreach ($resource[ 'middlewares' ] as $middleware)
                    {
                        Middleware::init ( $middleware );
                    }
                }

                if (empty( $args ))
                {
                    return $handler();
                }

                return call_user_func_array ( $handler , $args );
            }
        }
        static::$count++;
    }

    /**
     * @return string
     */
    private static function getRequestMethod ()
    {
        $method = $_SERVER[ 'REQUEST_METHOD' ];

        if ($_SERVER[ 'REQUEST_METHOD' ] == 'HEAD')
        {
            $method = 'GET';
        }
        elseif ($_SERVER[ 'REQUEST_METHOD' ] == 'POST')
        {
            $headers = getallheaders ();
            if (isset( $headers[ 'X-HTTP-Method-Override' ] ) &&
                in_array ( $headers[ 'X-HTTP-Method-Override' ] , array( 'PUT' , 'DELETE' , 'PATCH' ) )
            )
            {
                $method = $headers[ 'X-HTTP-Method-Override' ];
            }
        }
        return $method;
    }

    /**
     * @param $requestUri
     * @param $resource
     * @param $patterns
     * @return array
     */
    protected static function parseRoute ( $requestUri , $resource , $patterns ): array
    {
        $route = preg_replace_callback ( '/({.+?})/' , function ( $matches ) use ( $patterns )
        {
            $matches[ 0 ] = str_replace ( [ '{' , '}' ] , '' , $matches[ 0 ] );

            $all = '[a-zA-Z0-9_=\-\?]+';

            $normalize = ( substr ( $matches[ 0 ] , -1 ) == '?' ) ? substr ( $matches[ 0 ] , 0 , -1 ) : $matches[ 0 ];
            if (in_array ( $normalize , array_keys ( $patterns ) )) {
                if ($matches[ 0 ][ strlen ( $matches[ 0 ] ) - 1 ] == '?') {
                    return '?(\/' . $patterns[ $normalize ] . ')?';
                } else {
                    return $patterns[ $normalize ];
                }
            }
            if ($matches[ 0 ][ strlen ( $matches[ 0 ] ) - 1 ] == '?') {
                return '?(\/' . $all . ')?';
            }
            return $all;
        } , $resource );

        $regUri = explode ( '/' , str_replace ( '?}' , '}' , $resource ) );
        $args = array_diff ( array_replace ( $regUri , explode ( '/' , $requestUri ) ) , $regUri );
        $requestParam = array_map ( function ( $item ) {
            if (strpos ( $item , '{' ) !== false) {
                return str_replace ( [ '{' , '}' ] , '' , $item );
            }
        } , $regUri );
        $requestParam = array_filter ( $requestParam );
        $requestParam = array_slice ( $requestParam , 0 , count ( $args ) );
        $requestParam = @array_combine ( array_values ( $requestParam ) , array_values ( $args ) );
        return array( array_values ( $args ) , $resource , $route , $requestParam );
    }

    public function __call ( $method , $arguments )
    {
        return static::__callStatic ( $method , $arguments );
    }

    public static function __callStatic ( $method , $arguments )
    {
        if (in_array ( strtoupper ( $method ) , [ 'GET' , 'POST' , 'PUT' , 'DELETE' , 'OPTIONS' , 'PATCH' ] ))
        {
            static::addRoute ( strtoupper ( $method ) , ...$arguments );

            return ( new static );
        }
        else
        {
            throw new \BadMethodCallException( "Call to undefined method {$method}" );
        }
    }

    public function name ( $name )
    {
        $index = count ( static::$routes[ static::$method ] ) - 1;
        static::$routes[ static::$method ][ $index ][ 'name' ] = $name;
        return $this;
    }

    /**
     * @param $extension
     * @return $this
     */
    public function middleware ( $extension )
    {
        $index = count ( static::$routes[ static::$method ] ) - 1;
        static::$routes[ static::$method ][ $index ][ 'middleware' ][] = $extension;
        return $this;
    }

    /**
     * @param array $pattern
     * @return Router
     */
    public function pattern ( array $pattern = [] ): Router
    {
        $index = count ( static::$routes[ static::$method ] ) - 1;
        static::$routes[ static::$method ][ $index ][ 'pattern' ] = $pattern;
        return $this;
    }

    public function __destruct ()
    {
    }


}
