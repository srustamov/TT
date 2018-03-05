<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */


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


    private static $namespace = 'App\Controllers';

    private static $count = 0;

    private static $prefix = '';

    private static $method = '';

    private static $path = '';

    private static $middleware = [];

    private static $isAjax = false;

    private static $domain;


    /**
     * @param String $namespace
     */
    public static function setNamespace(String $namespace)
    {
        self::$namespace = trim($namespace, '\\');
    }

    /**
     * @param array $methods
     * @param $path
     * @param $handler
     * @return static
     */
    public static function any(array $methods, $path, $handler)
    {
        $instance = new static();

        foreach ($methods as $method) {
            self::add(strtoupper($method), $path, $handler);
        }
        return $instance;
    }

    /**
     * @param $method
     * @param $path
     * @param $handler
     * @internal param array $pattern
     * @internal param array $middleware
     */
    public static function add($method, $path, $handler)
    {
        static::$method = $method;
        static::$path   = $path;

        array_push(static::$routes[ $method ], [
            'path' => static::$prefix . strtolower($path) ,
            'handler' => $handler ,
            'isAjax' => static::$isAjax ,
            'middlewares' => self::$middleware
        ]);
    }

    /**
     * @param $path
     * @param $handler
     * @return static
     */
    public static function form($path, $handler)
    {
        $instance = new static();

        foreach ([ 'GET' , 'POST' ] as $method) {
            self::add($method, $path, $handler);
        }
        return $instance;
    }

    /**
     * @param $handler
     */
    public static function ajax($handler)
    {
        static::$isAjax = true;
        call_user_func($handler, new Router);
        static::$isAjax = false;
    }

    /**
     * @param $prefix
     * @return static
     */
    public static function prefix($prefix)
    {
        $instance = new static();

        static::$prefix = $prefix;

        return $instance;
    }

    /**
     * @param $prefix
     * @param $callback
     * @return null
     */
    public static function group($prefix, $callback = null)
    {
        $_prefix = $prefix;

        $middleware = false;

        $domain = false;

        if (is_null($callback)) {
            $callback = $prefix;

            $_prefix = static::$prefix;
        }

        if (is_array($prefix)) {
            if (isset($prefix[ 'prefix' ])) {
                $_prefix = $prefix[ 'prefix' ];
            }

            if (isset($prefix[ 'domain' ])) {
                if ($_SERVER[ 'HTTP_HOST' ] != $prefix[ 'domain' ]) {
                    return null;
                }

                self::$domain = $prefix[ 'domain' ];

                $domain = true;
            }
        }

        $requestUri = static::getrequestUri();

        $prefixUri = strtolower(static::$prefix . $_prefix);

        if ($prefixUri == substr($requestUri, 0, strlen(static::$prefix . $_prefix))) {
            if (isset($prefix[ 'middleware' ])) {
                self::$middleware[] = $prefix[ 'middleware' ];

                $middleware = true;
            }

            static::$prefix .= $_prefix;

            call_user_func($callback, new Router);

            if ($middleware && isset(self::$middleware[ count(self::$middleware) - 1 ])) {
                unset(self::$middleware[ count(self::$middleware) - 1 ]);
                $middleware = false;
            }
            static::$prefix = substr(static::$prefix, 0, strlen(static::$prefix) - strlen($_prefix));

            if ($domain) {
                self::$domain = null;
            }
        }
    }


    /**
     * @return String
     */
    protected static function getRequestUri(): String
    {
        $request_uri = urldecode(
            parse_url($_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH)
        );
        $request_uri = str_replace(' ', '', $request_uri);

        if($request_uri != '/') {
          $request_uri = rtrim($request_uri,'/');
        }

        if (!is_null(self::$domain)) {
            $request_uri = self::$domain . $request_uri;
        }

        return $request_uri;
    }


    /**
     * @return bool|int|mixed|void
     */
    protected static function match()
    {
        $requestUri = static::getRequestUri();

        $method     = static::getRequestMethod();

        $isAjax     = app('http')->isAjax();

        foreach (static::$routes[ $method ] as $resource) {
            if ($resource['isAjax'] && !$isAjax) {
                continue;
            }

            $args = [];

            $route = $resource[ 'path' ] != '/' ? rtrim($resource[ 'path' ], '/') : $resource[ 'path' ];

            if (!is_null(self::$domain)) {
                $route = self::$domain . $route;
            }

            $handler = $resource[ 'handler' ];

            if (preg_match('/({.+?})/', $route)) {
                list($args, $uri, $route) = self::parseRoute($requestUri, $route, $resource[ 'pattern' ] ?? []);
            }


            if (!preg_match("#^$route$#", $requestUri)) {
                unset(self::$routes[ $method ]);
                continue;
            }


            if (isset($uri)) {
                preg_match_all('/{(.+?)}/', $uri, $_request_keys);

                $_request_keys = array_map(function ($item) {
                    return str_replace('?', '', $item);
                }, $_request_keys[1]);

                $_request_data = array_combine(array_slice($_request_keys, 0, count($args)), $args);

                foreach ($_request_data as $key => $value) {
                    $_REQUEST[$key] = $value;
                }
            }


            if (is_string($handler) && strpos($handler, '@')) {
                list($controller, $method) = explode('@', $handler);

                if (strpos($controller, '/') !== false) {
                    $controller = str_replace('/', '\\', $controller);
                }
                $controller = "\\" .static::$namespace."\\$controller";

                $args = static::getReflectionMethodParameters($controller, $method, $args);

                if (method_exists($controller, $method)) {
                    $_SERVER[ 'CALLED_METHOD' ]     = $method;
                    $_SERVER[ 'CALLED_CONTROLLER' ] = substr($controller, strlen(static::$namespace)+2);
                    if (!empty($resource[ 'middlewares' ])) {
                        foreach ($resource[ 'middlewares' ] as $middleware) {
                            Middleware::init($middleware);
                        }
                    }

                    return call_user_func_array([$controller,$method],$args);
                } else {
                    return abort(404);
                }
            }


            if (!empty($resource[ 'middlewares' ])) {
                foreach ($resource[ 'middlewares' ] as $middleware) {
                    Middleware::init($middleware);
                }
            }

            $args = static::getReflectionFunctionParameters($handler, $args);

            return call_user_func_array($handler, $args);
        }
        static::$count++;
    }

    /**
     * @return string
     */
    private static function getRequestMethod()
    {
        $method = $_SERVER[ 'REQUEST_METHOD' ] ?? 'GET';

        if ($method == 'HEAD') {
            $method = 'GET';
        } elseif ($method == 'POST') {
            $headers = getallheaders();
            if (isset($headers[ 'X-HTTP-Method-Override' ]) &&
                in_array($headers[ 'X-HTTP-Method-Override' ], array( 'PUT' , 'DELETE' , 'PATCH' ))
            ) {
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
    private static function parseRoute($requestUri, $resource, $patterns): array
    {
        $route = preg_replace_callback('/({.+?})/', function ($matches) use ($patterns) {
            $matches[ 0 ] = str_replace([ '{' , '}' ], '', $matches[ 0 ]);

            $normalize = (substr($matches[ 0 ], -1) == '?') ? substr($matches[ 0 ], 0, -1) : $matches[ 0 ];

            if (in_array($normalize, array_keys($patterns))) {
                if ($matches[ 0 ][ strlen($matches[ 0 ]) - 1 ] == '?') {
                    return '?(\/' . $patterns[ $normalize ] . ')?';
                } else {
                    return $patterns[ $normalize ];
                }
            }
            if ($matches[ 0 ][ strlen($matches[ 0 ]) - 1 ] == '?') {
                return '?(\/[a-zA-Z0-9_=\-\?]+)?';
            }
            return '[a-zA-Z0-9_=\-\?]+';
        }, $resource);


        $regUri = explode('/', str_replace('?}', '}', $resource));

        $args   = array_diff(array_replace($regUri, explode('/', $requestUri)), $regUri);

        return array( array_values($args) , $resource , $route);
    }


    /**
     * @param $class_name
     * @param $method_name
     * @param $args
     * @return mixed
     */
    private static function getReflectionMethodParameters($class_name, $method_name, $args)
    {
        $reflection = new \ReflectionMethod($class_name, $method_name);

        foreach ($reflection->getParameters() as $num => $param) {
            if ($param->getClass()) {
                $class = $param->getClass()->name;
                $args[$num] = new $class();
            } else {
                if (!isset($args[$num])) {
                    if ($param->isDefaultValueAvailable()) {
                        $args[$num] = $param->getDefaultValue();
                    }
                }
            }
        }

        return $args;
    }


    /**
     * @param $function_name
     * @param $args
     * @return mixed
     */
    private static function getReflectionFunctionParameters($function_name, $args)
    {
        $reflection = new \ReflectionFunction($function_name);

        foreach ($reflection->getParameters() as $num => $param) {
            if ($param->getClass()) {
                $class = $param->getClass()->name;
                $args[$num] = new $class();
            } else {
                if (!isset($args[$num])) {
                    if ($param->isDefaultValueAvailable()) {
                        $args[$num] = $param->getDefaultValue();
                    }
                }
            }
        }

        return $args;
    }
    /**
     * @param $method
     * @param $args
     * @return Router
     */
    public function __call($method, $args)
    {
        return static::__callStatic($method, $args);
    }

    /**
     * @param $method
     * @param $args
     * @return static
     */
    public static function __callStatic($method, $args)
    {
        $instance = new static();

        $methods  = array( 'GET' , 'POST' , 'PUT' , 'DELETE' , 'OPTIONS' , 'PATCH' );

        if (in_array(strtoupper($method), $methods)) {
            self::add(strtoupper($method), ...$args);
            return $instance;
        } else {
            throw new \BadMethodCallException("Call to undefined method {$method}");
        }
    }

    /**
     * @param $extension
     * @return $this
     */
    public function middleware($extension) :Router
    {
        $index = count(static::$routes[ static::$method ]) - 1;
        static::$routes[ static::$method ][ $index ][ 'middleware' ][] = $extension;
        return $this;
    }

    /**
     * @param array $pattern
     * @return Router
     */
    public function pattern(array $pattern): Router
    {
        $index = count(static::$routes[ static::$method ]) - 1;
        static::$routes[ static::$method ][ $index ][ 'pattern' ] = $pattern;
        return $this;
    }

    /**
     * @return int
     */
    public static function init()
    {
        static::match();

        if (self::$count > 0) {
            return abort(404);
        }
    }
}
