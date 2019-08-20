<?php namespace System\Engine\Http\Routing\Traits;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



trait RouteGroup
{



  /**
   * @param $group_parameters
   * @param Closure $callback
   * @return null|mixed
   */
    public function group($group_parameters, \Closure $callback)
    {
        $parameters = $this->prepareGroupParameters($group_parameters);

        call_user_func($callback, $this);

        $this->restoreGroupParameters(...$parameters);
    }


    /**
     * @param $group_parameters
     * @return array|string
     */
    private function prepareGroupParameters($group_parameters)
    {
        if(is_string($group_parameters)) {
            $prefix = $group_parameters;
            $this->prefix .= trim($prefix);

            return array(false,$prefix,false ?? false,false);
        }

        $prefix     = $group_parameters['prefix'] ?? '';

        $middleware = $group_parameters['middleware'] ?? false;

        $domain     = $group_parameters[ 'domain' ] ?? false;

        if (isset($group_parameters[ 'name' ])) {
            $name = $group_parameters[ 'name' ];

            $this->group_name .= $name;
        }

        if ($domain) {
            $this->domain(trim($domain, '/'));
        }

        if ($middleware) {
            if (!is_array($middleware)) {
                $middleware = [$middleware];
            }

            $this->group_middleware = array_merge($this->group_middleware, $middleware);
        }

        $this->prefix .= trim($prefix);

        return array($middleware,$prefix,$name ?? false,$domain);
    }



    private function restoreGroupParameters($middleware, $prefix, $name, $domain)
    {
        if ($middleware && !empty($middleware)) {
            $this->group_middleware = array_slice($this->group_middleware, 0, -count($middleware));
        }

        if ($prefix && !empty(trim($prefix))) {
            $this->prefix = substr($this->prefix, 0, - strlen(trim($prefix)));
        }

        if ($name) {
            $this->group_name = substr($this->group_name, 0, - strlen($name));

            if (empty($this->group_name)) {
                $this->group_name = null;
            }
        }

        if ($domain) {
            $this->domain = null;
        }
    }
}
