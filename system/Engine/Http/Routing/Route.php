<?php namespace System\Engine\Http\Routing;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */




use System\Facades\Load;
use System\Engine\Http\Middleware;
use System\Exceptions\RouteException;

class Route
{

    use RouteMethodsTrait;
    use RouteGroupTrait;

    protected $routes = [
        'GET'     => [] ,
        'POST'    => [] ,
        'PUT'     => [] ,
        'DELETE'  => [] ,
        'OPTIONS' => [] ,
        'PATCH'   => [] ,
        'NAMES'   => [] ,
    ];

    private $patterns = [];

    private $domain;

    private $notFound = false;

    private $namespace = 'App\Controllers';

    private $prefix;

    private $name;

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


    public function setGlobalPatterns(Array $pattrens)
    {
        $this->patterns = $pattrens;
    }


    public function domain(String $domain = null)
    {
        if(!is_null($domain))
        {
            if(preg_match('/^https?:\/\//',$domain))
            {
                $domain = str_replace(['https://','http://'],'',$domain);
            }

            $this->domain = Load::class('url')->scheme().'://'.$domain;

            return $this;
        }
        else
        {
            $domain =  !is_null($this->domain)
                ? $this->domain
                : Load::class('url')->base();

            return rtrim($domain,'/');
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

        $middleware = [];

        $pattern    = [];

        $url        = $path;

        if(is_array($path))
        {

            if(isset($path['path'])) {
                $url  = (string) $path['path'];
            } else {
                throw new \InvalidArgumentException("Route argument path (url) required");
            }

            $middleware = isset($path['middleware'])
                ? is_array($path['middleware'])
                    ? $path['middleware']
                    : [$path['middleware']]
                : $middleware;

            $pattern    = $path['pattern'] ?? $pattern;

        }

        if(!empty($this->patterns))
        {
            $pattern = array_merge($this->patterns ,$pattern);
        }

        $middleware_array = $this->group_middleware;

        if(!empty($middleware))
        {
            $middleware_array = array_merge($middleware_array,$middleware);
        }

        $_path = rtrim($this->domain().'/'.trim($this->prefix.strtolower($url),'/'),'/');

        if(isset($path['name'])) {
          $this->routes['NAMES'][$this->name.$path['name']] = $_path;
        }

        foreach ($this->methods as $method) {
            array_push($this->routes[ strtoupper($method) ], [
                'path' => $_path ,
                'handler' => $handler ,
                'ajax' => $this->ajax ,
                'middleware' => $middleware_array,
                'pattern' => $pattern
            ]);
        }

    }


    /**
     * @param Closure $handler
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
        $requestUri = trim(Load::class('url')->current(),'/');

        $method     = $this->getRequestMethod();

        $ajax       = Load::class('http')->isAjax();

        foreach ($this->routes[ $method ] as $resource)
        {
            if ($resource['ajax'] && !$ajax)
            {
                continue;
            }

            $args    = [];

            $route   = $resource[ 'path' ];

            $handler = $resource[ 'handler' ];

            if (preg_match('/({.+?})/', $route))
            {
                list($args, $uri, $route) = $this->parseRoute($requestUri, $route, $resource[ 'pattern' ] ?? []);
            }

            if (!preg_match("#^$route$#", $requestUri))
            {
                unset($this->routes[ $method ]);
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
                return $this->callAction($handler,$resource['middleware'],$args);
            }
            elseif (is_callable($handler))
            {
                return $this->callHandler($handler,$resource['middleware'],$args);
            }
            else
            {
                throw new RouteException("Route Handler type undefined");
            }

        }
        $this->notFound = true;
    }



    protected function callAction(String $action,$middleware_array,$args)
    {
        list($controller, $method) = explode('@', $action);

        if (strpos($controller, '/') !== false)
        {
            $controller = str_replace('/', '\\', $controller);
        }

        $controller_with_namespace = "\\" .$this->namespace."\\$controller";

        if (method_exists($controller_with_namespace, $method))
        {
            define('CALLED_CONTROLLER_METHOD',strtolower($method));

            define('CALLED_CONTROLLER',$controller);

            if (!empty($middleware_array))
            {
                foreach ($middleware_array as $middleware)
                {
                    Middleware::init($middleware);
                }
            }

            $args = $this->getReflectionMethodParameters($controller_with_namespace, $method, $args);

            if (\method_exists($controller_with_namespace,'__construct')) {
                $contstructorArgs = $this->getReflectionMethodParameters($controller_with_namespace, '__construct');
            } else {
                $contstructorArgs = [];
            }

            $content = call_user_func_array([new $controller_with_namespace(...$contstructorArgs),$method],$args);

            $this->response($content);

        }
        else
        {
            abort(404);
        }
    }



    protected function callHandler(Callable $handler,$middleware_array,$args)
    {
        if (!empty($middleware_array))
        {
            foreach ($middleware_array as $middleware)
            {
                Middleware::init($middleware);
            }
        }

        $args    = $this->getReflectionFunctionParameters($handler, $args);

        $content = call_user_func_array($handler,$args);

        $this->response($content);
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
     * @param $requestUri
     * @param $resource
     * @param $patterns
     * @return array
     */
    private function parseRoute($requestUri, $resource, $patterns): array
    {

        $callback = function ($matches) use ($patterns)
        {
            $normalize = str_replace('?','',$matches[1]);

            if (in_array($normalize, array_keys($patterns)))
            {
                if (strpos($matches[1],'?') !== false)
                {
                    return '?(\/' . $patterns[ $normalize ] . ')?';
                }
                else
                {
                    return $patterns[ $normalize ];
                }
            }
            else
            {
              if (strpos($matches[1],'?') !== false)
              {
                  return '?(\/[a-zA-Z0-9_=\-\?]+)?';
              }
              else
              {
                  return '[a-zA-Z0-9_=\-\?]+';
              }
            }

        };

        $route  = preg_replace_callback('/{(.+?)}/',$callback, $resource);

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
    private function getReflectionMethodParameters($class_name, $method, $args = [])
    {
        $reflection = new \ReflectionMethod($class_name, $method);

        foreach ($reflection->getParameters() as $num => $param)
        {

            if ($param->getClass()) {

                $class = $param->getClass()->name;

                if(($instance = Load::applicationClasses($class,true)))
                {
                    $args[$num] = Load::class($instance);
                }
                else
                {
                    $args[$num] = new $class();
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
    private function getReflectionFunctionParameters($function, $args = [])
    {
        $reflection  = new \ReflectionFunction($function);

        foreach ($reflection->getParameters() as $num => $param)
        {

            if ($param->getClass()) {

                $class = $param->getClass()->name;

                if(Load::applicationClasses($class,true))
                {
                    $args[$num] = Load::class($class);
                }
                else
                {
                    $args[$num] = new $class();
                }
            }
        }
        return $args;

    }


    /**
     * @param $extension
     * @return void
     */
    public function middleware($extension)
    {
        if(!empty($this->methods))
        {
            foreach ($this->methods as $method) {

                $index = count($this->routes[ $method ]) - 1;

                $this->routes[ $method ][ $index ][ 'middleware' ][] = $extension;
            }
        }
    }


    /**
     * @param array|string $name
     * @param null|string $value
     * @return void
     */
    public function pattern($name,$value = null)
    {
        $pattern = is_array($name) ? $name : [$name => $value];

        if(!empty($this->methods))
        {
            foreach ($this->methods as $method) {

                $index = count($this->routes[ $method ]) - 1;

                $old = $this->routes[ $method ][ $index ][ 'pattern' ];

                $this->routes[ $method ][ $index ][ 'pattern' ] = array_merge($old,$pattern);
            }
        }
    }


    /**
     * @param string $name
     * @return void
     */
    public function name(String $name)
    {
        if(!empty($this->methods))
        {
            foreach ($this->methods as $method) {

                $index = count($this->routes[ $method ]) - 1;

                $path = $this->routes[ $method ][ $index ][ 'path' ];

                $this->routes['NAMES'][$this->name.$name] = $path;
            }
        }
    }



    protected function response($content)
    {
        Load::class('response')->setContent($content);
    }


    public function getRoutes()
    {
        return (array) $this->routes;
    }


    public function getName($name,Array $parameters = [])
    {
      if(isset($this->routes['NAMES'][$name])) {
        $route =  $this->routes['NAMES'][$name];

        if(strpos($route,'}') !== false) {
          if(!empty($parameters)) {
            foreach ($parameters as $key => $value) {
              $route = str_replace(['{'.$key.'}','{'.$key.'?}'],$value,$route);
            }
          }

          $callback = function($match)
          {
              if(strpos($match[0],'?') !== false) {
                return '';
              } else {
                return $match[0];
              }
          };

          $route = preg_replace_callback('/({.+?})/',$callback,$route);
        }

        if(strpos($route,'}') !== false) {
          throw new RouteException("Route url parameters required");
        }

        return $route;

      }
      else {
        throw new RouteException("Route name [{$name}] not found");
      }

    }



    public function execute(Array $routes = null)
    {
        if(!is_null($routes)) {
            $this->routes = $routes;
        }

        $this->run();

        if ($this->notFound)
        {
           abort(404);
        }

    }

}
