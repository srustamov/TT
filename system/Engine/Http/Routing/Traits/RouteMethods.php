<?php namespace System\Engine\Http\Routing\Traits;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



trait RouteMethods
{
    public function get($path, $handler)
    {
        $this->add(['GET'], $path, $handler);

        return $this;
    }

    public function post($path, $handler)
    {
        $this->add(['POST'], $path, $handler);

        return $this;
    }

    public function put($path, $handler)
    {
        $this->add(['PUT'], $path, $handler);

        return $this;
    }

    public function delete($path, $handler)
    {
        $this->add(['DELETE'], $path, $handler);

        return $this;
    }

    public function options($path, $handler)
    {
        $this->add(['OPTIONS'], $path, $handler);

        return $this;
    }

    public function patch($path, $handler)
    {
        $this->add(['PATCH'], $path, $handler);

        return $this;
    }

    public function form($path, $handler)
    {
        $this->add(['GET','POST'], $path, $handler);

        return $this;
    }

    public function any($path, $handler)
    {
        $this->add(['GET','POST','PUT','DELETE','OPTIONS','PATCH'], $path, $handler);

        return $this;
    }
}
