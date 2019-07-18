<?php namespace System\Engine\Http\Routing;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */




use System\Engine\App;
use System\Engine\Load;
use System\Engine\Reflections;
use System\Engine\Http\Middleware;
use System\Engine\Http\Response;
use System\Exceptions\RouteException;
use System\Exceptions\NotFoundException;

class Route
{
    use Traits\RouteMethods;
    use Traits\RouteGroup;
    use Traits\Parse;

    protected $routes = [
        'GET'     => [] ,
        'POST'    => [] ,
        'PUT'     => [] ,
        'DELETE'  => [] ,
        'OPTIONS' => [] ,
        'PATCH'   => [] ,
        'NAMES'   => [] ,
    ];


    private $middleware_alias = [];

    private $patterns = [];

    private $domain;

    private $notFound = false;

    private $namespace = 'App\Controllers';

    private $prefix;

    private $group_name;

    private $name;

    private $middleware = [];

    private $pattern = [];

    private $methods = [];

    private $group_middleware = [];

    private $ajax = false;


    /**
     * @param String $namespace
     */
    public function setNamespace(String $namespace)
    {
        $this->namespace = trim($namespace, '\\');
    }


    public function setGlobalPatterns(array $pattrens)
    {
        $this->patterns = $pattrens;
    }


    public function domain(String $domain = null)
    {
        if (!is_null($domain)) {
            if (preg_match('/^https?:\/\//', $domain)) {
                $domain = str_replace(['https://','http://'], '', $domain);
            }

            $this->domain = Load::class('url')->scheme().'://'.$domain;

            return $this;
        } else {
            $domain =  !is_null($this->domain)
                ? $this->domain
                : Load::class('url')->base();

            return rtrim($domain, '/');
        }
    }



    /**
     * @param $methods
     * @param $path
     * @param $handler
     */
    public function add($methods, $path, $handler)
    {
        $this->methods = is_array($methods) ? $methods : [$methods];


        list($_path,$middleware,$pattern) = $this->parsePath($path);


        foreach ($this->methods as $method) {
            $this->routes[ strtoupper($method) ][] =  [
                'path' => $_path ,
                'handler' => $handler ,
                'ajax' => $this->ajax ,
                'middleware' => $middleware,
                'pattern' => $pattern
            ];
        }

        $this->name = null;

        $this->pattern = [];

        $this->middleware = [];
    }


    /**
     * @param \Closure $handler
     */
    public function ajax(\Closure $handler)
    {
        $this->ajax = true;

        call_user_func($handler);

        $this->ajax = false;
    }


    /**
     * @return bool|int|mixed|void
     * @throws RouteException
     */
    protected function run()
    {
        $requestUri = trim(Load::class('url')->current(), '/');

        $method     = $this->getRequestMethod();

        $ajax       = Load::class('http')->isAjax();

       

        foreach ($this->routes[ $method ] as $resource) {

            if ($resource['ajax'] && !$ajax) {
                continue;
            }

            $args    = [];

            $route   = $resource[ 'path' ];

            $handler = $resource[ 'handler' ];

            if (preg_match('/({.+?})/', $route)) {
                list($args, $uri, $route) = $this->parseRoute($requestUri, $route, $resource[ 'pattern' ] ?? []);
            }

            if (!preg_match("#^$route$#", $requestUri)) {
                unset($this->routes[ $method ]);
                continue;
            }

            if (isset($uri)) {
                $this->parseRouteParams($uri,$args);
            }

            if (is_string($handler) && strpos($handler, '@')) {
                return $this->callAction($handler, $resource['middleware'], $args);
            } elseif (is_callable($handler)) {
                return $this->callHandler($handler, $resource['middleware'], $args);
            } else {
                throw new RouteException("Route Handler type undefined");
            }
        }
        throw new NotFoundException;
    }



    protected function callAction(String $action, $middleware_array, $args)
    {
        list($controller, $method) = explode('@', $action);

        if (strpos($controller, '/') !== false) {
            $controller = str_replace('/', '\\', $controller);
        }

        $controller_with_namespace = "\\" .$this->namespace."\\$controller";

        if (method_exists($controller_with_namespace, $method)) {
            define('ACTION', strtolower($method));

            define('CONTROLLER', $controller);

            if (!empty($middleware_array)) {
                foreach ($middleware_array as $middleware) {
                    if (isset($this->middlewareAliases[$middleware])) {
                        Middleware::init($this->middlewareAliases[$middleware], true);
                    }
                }
            }

            $args = Reflections::classMethodParameters($controller_with_namespace, $method, $args);

            if (method_exists($controller_with_namespace, '__construct')) {
                $constructorArgs = Reflections::classMethodParameters($controller_with_namespace, '__construct');
            } else {
                $constructorArgs = [];
            }

            $content = call_user_func_array([new $controller_with_namespace(...$constructorArgs),$method], $args);

            if(Load::isInstance($content,'response')) {
                return $content;
            }
            return Load::class('response')->setContent($content);
        } else {
            throw new NotFoundException;
        }
    }



    protected function callHandler(callable $handler, $middleware_array, $args)
    {
        if (!empty($middleware_array)) {
            foreach ($middleware_array as $middleware) {
                if (isset($this->middlewareAliases[$middleware])) {
                    Middleware::init($this->middlewareAliases[$middleware], true);
                }
            }
        }

        $args = Reflections::functionParameters($handler, $args);

        $content = call_user_func_array($handler, $args);

        if (Load::isInstance($content, 'response')) {
            return $content;
        }


        return Load::class('response')->setContent($content);
    }


    /**
     * @return string
     */
    private function getRequestMethod()
    {
        $method = Load::class('request')->method('GET');

        return ($method == 'HEAD') ?  'GET' : $method;
    }


    /**
     * @param $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        if (!empty($this->methods)) {
            foreach ($this->methods as $method) {
                $index = count($this->routes[ $method ]) - 1;

                $this->routes[ $method ][ $index ][ 'middleware' ][] = $middleware;
            }
        } else {
            $this->middleware[] = $middleware;
        }

        return $this;
    }


    /**
     * @param array|string $name
     * @param null|string $value
     * @return void
     */
    public function pattern($name, $value = null)
    {
        $pattern = is_array($name) ? $name : [$name => $value];

        if (!empty($this->methods)) {
            foreach ($this->methods as $method) {
                $index = count($this->routes[ $method ]) - 1;

                $old = $this->routes[ $method ][ $index ][ 'pattern' ];

                $this->routes[ $method ][ $index ][ 'pattern' ] = array_merge($old, $pattern);
            }
        } else {
            $this->pattern = $pattern;
        }

        return $this;
    }


    /**
     * @param string $name
     * @return void
     */
    public function name(String $name)
    {
        if (!empty($this->methods)) {
            foreach ($this->methods as $method) {
                $index = count($this->routes[ $method ]) - 1;

                $path = $this->routes[ $method ][ $index ][ 'path' ];

                $this->routes['NAMES'][$this->group_name.$name] = $path;
            }
        } else {
            $this->name = $name;
        }

        return $this;
    }



    public function getRoutes()
    {
        return (array) $this->routes;
    }


    public function getName($name, array $parameters = [])
    {
        if (isset($this->routes['NAMES'][$name])) {
            $route =  $this->routes['NAMES'][$name];

            if (strpos($route, '}') !== false) {
                if (!empty($parameters)) {
                    foreach ($parameters as $key => $value) {
                        $route = str_replace(['{'.$key.'}','{'.$key.'?}'], $value, $route);
                    }
                }

                $callback = function ($match) {
                    if (strpos($match[0], '?') !== false) {
                        return '';
                    } else {
                        return $match[0];
                    }
                };

                $route = preg_replace_callback('/({.+?})/', $callback, $route);

                if (strpos($route, '}') !== false) {
                    throw new RouteException("Route url parameters required");
                }
            }

            return $route;
        }
        throw new RouteException("Route name [{$name}] not found");
        
    }



    public function execute(App $app, $routeMiddleware):Response
    {
        $this->middlewareAliases = $routeMiddleware;

        if (file_exists($file = $app->routesCacheFile())) {
            $this->routes = require_once $file;
        } else {
            foreach (glob($app->path('routes')."/*") as $file) {
                require_once $file;
            }
        }

        return !CONSOLE ? $this->run() : Load::class('response');
    }
}
