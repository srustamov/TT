<?php namespace System\Engine\Http\Routing\Traits;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use System\Engine\Http\Parameters;
use System\Engine\Load;

 trait Parse
 {

    public function parsePath($path)
    {

        $middleware = $this->middleware;

        $pattern = $this->pattern;

        $url = $path;

        
        if (is_array($path)) {

            $parameters = new Parameters($path);

            if ($parameters->has('path')) {
                $url  = (string) $parameters->get('path');
            } else {
                throw new \InvalidArgumentException("Route argument path (url) required");
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

        $_path = rtrim($this->domain().'/'.trim($this->prefix.strtolower($url), '/'), '/');

        if (isset($parameters) && $parameters->has('name')) {
            $this->routes['NAMES'][$this->group_name.$parameters->get('name')] = $_path;
        } elseif (!is_null($this->name)) {
            $this->routes['NAMES'][$this->group_name.$this->name] = $_path;
        }

        return array($_path,$middleware_array,$pattern);
    }


    public function parseRouteParams($uri,$args)
    {
        preg_match_all('/{(.+?)}/', $uri, $keys);

        $keys = array_map(function ($item) {
            return str_replace('?', '', $item);
        }, $keys[1]);

        $routeParams = array_combine(array_slice($keys, 0, count($args)), $args);

        Load::class('request')->setRouteParams($routeParams);

    }



    /**
     * @param $requestUri
     * @param $resource
     * @param $patterns
     * @return array
     */
    public function parseRoute($requestUri, $resource, $patterns): array
    {
        $callback = function ($matches) use ($patterns) {
            $normalize = str_replace('?', '', $matches[1]);

            if (in_array($normalize, array_keys($patterns))) {
                if (strpos($matches[1], '?') !== false) {
                    return '?(\/' . $patterns[ $normalize ] . ')?';
                } else {
                    return $patterns[ $normalize ];
                }
            } else {
                if (strpos($matches[1], '?') !== false) {
                    return '?(\/[a-zA-Z0-9_=\-\?]+)?';
                } else {
                    return '[a-zA-Z0-9_=\-\?]+';
                }
            }
        };

        $route  = preg_replace_callback('/{(.+?)}/', $callback, $resource);

        $regUri = explode('/', str_replace('?}', '}', $resource));

        $args   = array_diff(array_replace($regUri, explode('/', $requestUri)), $regUri);

        return array( array_values($args) , $resource , $route);
    }
     
 }
 