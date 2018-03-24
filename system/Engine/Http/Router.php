<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


use System\Facades\Load;


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

    private $notFound = 0;

    private $namespace = 'App\Controllers';

    private $prefix;

    private $method;

    private $path;

    private $middleware = [];

    private $ajax = false;


    /**
     * @param String $namespace
     */
    public function setNamespace(String $namespace)
    {
        $this->namespace = trim($namespace, '\\');
    }

    /**
     * @param array $methods
     * @param $path
     * @param $handler
     * @return Router
     */
    public function any(array $methods, $path, $handler)
    {
        foreach ($methods as $method)
        {
            $this->add(strtoupper($method), $path, $handler);
        }
        return $this;
    }

    /**
     * @param $method
     * @param $path
     * @param $handler
     */
    public function add($method, $path, $handler)
    {
        $this->method = $method;
        $this->path   = $path;

        array_push(static::$routes[ $method ], [
            'path' => $this->prefix . strtolower($path) ,
            'handler' => $handler ,
            'ajax' => $this->ajax ,
            'middleware' => $this->middleware,
            'pattern' => []
        ]);
    }

    /**
     * @param $path
     * @param $handler
     */
    public function form($path, $handler)
    {
        foreach ([ 'GET' , 'POST' ] as $method)
        {
            $this->add($method, $path, $handler);
        }
    }

    /**
     * @param $handler
     */
    public function ajax($handler)
    {
        $this->ajax = true;
        call_user_func($handler);
        $this->ajax = false;
    }

    /**
     * @param $prefix
     * @return static
     */
    public function prefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @param $prefix
     * @param $callback
     * @return null|mixed
     */
    public function group($prefix, $callback = null)
    {
        $_prefix    = $prefix;
        $middleware = false;
        $args       = [];

        if (is_null($callback))
        {
            $callback = $prefix;
            $_prefix = $this->prefix;
        }

        if (is_array($prefix))
        {
            if (isset($prefix[ 'prefix' ]))
            {
                $_prefix = $prefix[ 'prefix' ];
            }
            if (isset($prefix[ 'domain' ]))
            {
                $_domain = $prefix[ 'domain' ];

                if(preg_match('/({.+?})/',$prefix[ 'domain' ]))
                {
                    $_domain = preg_replace_callback('/({.+?})/',function() {
                        return '[A-Za-z0-9\-\_]+';
                    },$prefix[ 'domain' ]);

                    $r_domain = explode('.',$prefix[ 'domain' ]);

                    $args     = array_diff(array_replace($r_domain, explode('.', $this->server('http_host'))), $r_domain);
                }
                if (!preg_match("#^$_domain$#",$this->server('http_host')))
                {
                    return null;
                }
                else
                {
                    if(!empty($args))
                    {
                        preg_match_all('/{(.+?)}/', $prefix[ 'domain' ], $_request_keys);

                        $_request_data = array_combine(array_slice($_request_keys, 0, count($args)), $args);

                        foreach ($_request_data as $key => $value)
                        {
                            $_REQUEST[$key] = $value;
                        }

                        Load::class('url')->setBase($prefix[ 'domain' ]);
                    }
                }
            }
        }

        $requestUri = Load::class('url')->request();

        $prefixUri = strtolower($this->prefix . $_prefix);

        if ($prefixUri == substr($requestUri, 0, strlen($this->prefix . $_prefix)))
        {
            if (isset($prefix[ 'middleware' ]))
            {
                $this->middleware[] = $prefix[ 'middleware' ];
                $middleware = true;
            }

            $this->prefix .= $_prefix;

            call_user_func_array($callback,$this->getReflectionFunctionParameters($callback,$args));

            if ($middleware && isset($this->middleware[ count($this->middleware) - 1 ]))
            {
                unset($this->middleware[ count($this->middleware) - 1 ]);
                $middleware = false;
            }
            $this->prefix = substr($this->prefix, 0, strlen($this->prefix) - strlen($_prefix));

            if (isset($prefix[ 'domain' ]))
            {
                Load::class('url')->setBase();
            }
        }
    }




    /**
     * @return bool|int|mixed|void
     * @throws \Exception
     */
    protected function match()
    {
        $requestUri = Load::class('url')->request();
        $method     = $this->getRequestMethod();
        $ajax       = Load::class('http')->isAjax();

        foreach (static::$routes[ $method ] as $resource)
        {

            if ($resource['ajax'] && !$ajax)
            {
                continue;
            }

            $args    = [];
            $route   = $resource[ 'path' ] != '/' ? rtrim($resource[ 'path' ], '/') : $resource[ 'path' ];
            $handler = $resource[ 'handler' ];

            if (preg_match('/({.+?})/', $route))
            {
                list($args, $uri, $route) = $this->parseRoute($requestUri, $route, $resource[ 'pattern' ] ?? []);
            }

            if (!preg_match("#^$route$#", $requestUri))
            {
                unset(self::$routes[ $method ]);
                continue;
            }

            if (isset($uri))
            {
                preg_match_all('/{(.+?)}/', $uri, $_request_keys);

                $_request_keys = array_map(function ($item)
                {
                    return str_replace('?', '', $item);
                }, $_request_keys[1]);

                $_request_data = array_combine(array_slice($_request_keys, 0, count($args)), $args);

                foreach ($_request_data as $key => $value)
                {
                    $_REQUEST[$key] = $value;
                }
            }

            if (is_string($handler) && strpos($handler, '@'))
            {
                list($controller, $method) = explode('@', $handler);

                if (strpos($controller, '/') !== false)
                {
                    $controller = str_replace('/', '\\', $controller);
                }

                $controller_with_namespace = "\\" .$this->namespace."\\$controller";

                if (method_exists($controller_with_namespace, $method))
                {
                    $this->server('called_method',$method);
                    $this->server('called_controller',$controller);

                    if (!empty($resource[ 'middleware' ]))
                    {
                        foreach ($resource[ 'middleware' ] as $middleware)
                        {
                            Middleware::init($middleware);
                        }
                    }

                    $args    = $this->getReflectionMethodParameters($controller_with_namespace, $method, $args);

                    $contstructorArgs = $this->getReflectionMethodParameters($controller_with_namespace, '__construct');

                    $content = call_user_func_array([new $controller_with_namespace($contstructorArgs),$method],$args);

                    return $this->response($content);

                }
                else
                {
                    return abort(404);
                }
            }
            elseif (is_callable($handler))
            {
                if (!empty($resource[ 'middleware' ]))
                {
                    foreach ($resource[ 'middleware' ] as $middleware)
                    {
                        Middleware::init($middleware);
                    }
                }

                $args    = $this->getReflectionFunctionParameters($handler, $args);

                $content = call_user_func_array($handler,$args);

                return $this->response($content);
            }
            else
            {
                throw new \Exception("Route Handler type undefined");
            }

        }
        $this->notFound++;
    }


    /**
     * @return string
     */
    private function getRequestMethod()
    {
        $method = ($this->server('request_method') == 'HEAD') ?  'GET' : $this->server('request_method');

        if ($method == 'POST')
        {
            $headers = getallheaders();

            $xhmo    = $headers[ 'X-HTTP-Method-Override' ] ?? false;

            if ($xhmo && in_array($xhmo, array( 'PUT' , 'DELETE' , 'PATCH' )))
            {
                $method = $xhmo;
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
    private function parseRoute($requestUri, $resource, $patterns): array
    {
        $route = preg_replace_callback('/({.+?})/', function ($matches) use ($patterns) {

            $matches[ 0 ] = str_replace([ '{' , '}' ], '', $matches[ 0 ]);

            $normalize = (substr($matches[ 0 ], -1) == '?') ? substr($matches[ 0 ], 0, -1) : $matches[ 0 ];

            if (in_array($normalize, array_keys($patterns)))
            {
                if ($matches[ 0 ][ strlen($matches[ 0 ]) - 1 ] == '?')
                {
                    return '?(\/' . $patterns[ $normalize ] . ')?';
                }
                else
                {
                    return $patterns[ $normalize ];
                }
            }

            if ($matches[ 0 ][ strlen($matches[ 0 ]) - 1 ] == '?')
            {
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
     * @param $method
     * @param $args
     * @return mixed
     */
    private  function getReflectionMethodParameters($class_name, $method, $args = [])
    {
        $reflection = new \ReflectionMethod($class_name, $method);

        $app_classes = array_flip(Load::config('config.classes'));

        foreach ($reflection->getParameters() as $num => $param)
        {

            if ($param->getClass()) {

                $class = $param->getClass()->name;

                if(in_array($class,$app_classes))
                {
                    $args[$num] = Load::class($app_classes[$class]);
                }
                else
                {
                    $args[$num] = new $class();
                }

            }
            else
            {
                if (!isset($args[$num]))
                {
                    if ($param->isDefaultValueAvailable())
                    {
                        $args[$num] = $param->getDefaultValue();
                    }
                }
            }
        }
        return $args;
    }


    /**
     * @param $function
     * @param $args
     * @return mixed
     */
    private  function getReflectionFunctionParameters($function, $args = [])
    {
        $reflection  = new \ReflectionFunction($function);

        $app_classes = array_flip(Load::config('config.classes'));

        foreach ($reflection->getParameters() as $num => $param)
        {

            if ($param->getClass()) {

                $class = $param->getClass()->name;

                if(in_array($class,$app_classes))
                {
                    $args[$num] = Load::class($app_classes[$class]);
                }
                else
                {
                    $args[$num] = new $class();
                }

            }
            else
            {
                if (!isset($args[$num]))
                {
                    if ($param->isDefaultValueAvailable())
                    {
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
        $methods  = array( 'GET' , 'POST' , 'PUT' , 'DELETE' , 'OPTIONS' , 'PATCH' );

        if (in_array(strtoupper($method), $methods))
        {
            $this->add(strtoupper($method), ...$args);

            return $this;
        }
        else
        {
            throw new \BadMethodCallException("Call to undefined method Route::{$method}()");
        }
    }

    /**
     * @param $extension
     * @return Router
     */
    public function middleware($extension) :Router
    {
        $index = count(static::$routes[ $this->method ]) - 1;
        static::$routes[ $this->method ][ $index ][ 'middleware' ][] = $extension;
        return $this;
    }

    /**
     * @param array $pattern
     * @return Router
     */
    public function pattern(array $pattern): Router
    {
        $index = count(static::$routes[ $this->method ]) - 1;
        static::$routes[ $this->method ][ $index ][ 'pattern' ] = $pattern;
        return $this;
    }




    private function server(String $key,$value = null)
    {
        if(!is_null($value))
        {
            $_SERVER[strtoupper($key)] = $value;
        }
        else
        {
            return $_SERVER[strtoupper($key)] ?? false;
        }
    }




    private function response($content)
    {
        if(is_array($content))
        {
            echo '['.implode(',',$content).']';
        }
        else
        {
            echo $content;
        }
    }



    public function execute()
    {
        $this->match();

        if ($this->notFound > 0)
        {
            return abort(404);
        }
    }

}
