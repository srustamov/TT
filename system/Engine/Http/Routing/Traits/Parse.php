<?php namespace System\Engine\Http\Routing\Traits;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use System\Engine\Http\Parameters;

trait Parse
{
    public function parsePath($path): array
    {
        $middleware = $this->middleware;

        $pattern = $this->pattern;

        $url = $path;

        if (is_array($path)) {
            $parameters = new Parameters($path);

            if ($parameters->has('path')) {
                $url  = (string) $parameters->get('path');
            } else {
                throw new \InvalidArgumentException('Route argument path (url) required');
            }

            if ($parameters->has('middleware')) {
                $middleware = array_merge($middleware, (array) $parameters->get('middleware'));
            }

            if ($parameters->has('pattern')) {
                $pattern = array_merge($pattern, $parameters->get('pattern'));
            }
        }

        $pattern = array_merge($this->patterns, $pattern);

        $middleware_array = array_merge($this->group_middleware, $middleware);

        $_path = rtrim($this->domain().'/'.ltrim($this->prefix.strtolower($url), '/'));
        
        if (isset($parameters) && $parameters->has('name')) {
            $this->routes['NAMES'][$this->group_name.$parameters->get('name')] = $_path;
        } elseif ($this->name !== null) {
            $this->routes['NAMES'][$this->group_name.$this->name] = $_path;
        }

        return array($_path,$middleware_array,$pattern);
    }


    /**
     * @param $uri
     * @param $args
     * @throws \Exception
     */
    public function parseRouteParams($uri, $args)
    {
        preg_match_all('/{(.+?)}/', $uri, $keys);

        $keys = array_map(static function ($item) {
            return str_replace('?', '', $item);
        }, $keys[1]);

        $routeParams = array_combine(array_slice($keys, 0, count($args)), $args);

        $this->app::get('request')->setRouteParams($routeParams);
    }



    /**
     * @param $requestUri
     * @param $resource
     * @param $patterns
     * @return array
     */
    public function parseRoute($requestUri, $resource, $patterns): array
    {
        $callback = static function ($matches) use ($patterns) {
            $normalize = str_replace('?', '', $matches[1]);

            if (array_key_exists($normalize, $patterns)) {
                if (strpos($matches[1], '?') !== false) {
                    return '?(\/' . $patterns[ $normalize ] . ')?';
                }

                return $patterns[ $normalize ];
            }

            if (strpos($matches[1], '?') !== false) {
                return '?(\/[a-zA-Z0-9_=\-\?]+)?';
            }

            return '[a-zA-Z0-9_=\-\?]+';
        };

        $route  = preg_replace_callback('/{(.+?)}/', $callback, $resource);

        $regUri = explode('/', str_replace('?}', '}', $resource));

        $args   = array_diff(array_replace($regUri, explode('/', $requestUri)), $regUri);

        return array( array_values($args) , $resource , $route);
    }
}
