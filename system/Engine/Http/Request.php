<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


use function file_get_contents;
use function in_array;
use Countable;
use ArrayAccess;
use Serializable;
use System\Engine\App;

/**
 * @method all()
 * @method only(...$names)
 * @method except()
 * @method add()
 * @method has()
 * @method map($callback)
 * @method filter($callback)
 */
class Request implements ArrayAccess, Countable, Serializable
{
    /**@var Parameters */
    public $request = [];

    /**@var Parameters */
    public $query = [];

    /**@var Parameters */
    public $input = [];

    /**@var Parameters */
    public $files = [];

    /**@var Parameters */
    public $cookies = [];

    /**@var Parameters */
    public $server = [];

    /**@var Parameters */
    public $headers = [];

    public $routeParams = [];

    public $method;

    /**@var App */
    private $application;


    public function __construct(App $application)
    {
        $this->application = $application;

        $this->prepare();
    }


    public function prepare()
    {
        $this->server = new Parameters($_SERVER);

        $this->headers = new Parameters(getallheaders());

        $this->cookies = new Parameters($_COOKIE);

        $this->query = new Parameters($_GET);

        $this->input = new Parameters($this->prepareInputData());

        $this->files = new UploadedFile($_FILES);

        $this->method = $this->method('GET');

        if (0 === strpos($this->headers->get('Content-Type'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($this->method), ['PUT', 'DELETE', 'PATCH'])
        ) {
            $this->request = $this->input;
        } else {
            $this->request = new Parameters($_POST);
        }

        return $this;
    }


    protected function prepareInputData()
    {
        parse_str(file_get_contents('php://input'), $data);

        return $data;
    }


    public function setRouteParams($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $name => $_value) {
                $this->routeParams[$name] = $_value;
            }
        } else {
            $this->routeParams[$key] = $value;
        }
    }

    public function params($key)
    {
        return $this->routeParams[$key] ?? false;
    }


    public function get($key, $default = null)
    {
        if ($this !== $result = $this->query->get($key, $this)) {
            return $result;
        }

        if ($this !== $result = $this->request->get($key, $this)) {
            return $result;
        }

        return $default;
    }


    public function input($name = null, $default = false)
    {
        if ($name) {
            return $this->input->get($name, $default);
        }
        return $this->input;
    }


    public function session($key = null)
    {
        if ($key === null) {
            return $this->app('session');
        }

        return $this->app('session')->get($key);
    }


    public function cookie($key = null)
    {
        if ($key === null) {
            return $this->cookies;
        }

        return $this->cookies->get($key);
    }


    public function user()
    {
        return $this->app('authentication')->user();
    }


    public function server($key = null, $default = null)
    {
        if ($key === null) {
            return $this->server;
        }
        return $this->server->get(strtoupper($key), $default);
    }


    public function file($name)
    {
        $this->files->get($name);
    }


    public function method($default = 'GET'): String
    {
        if ($this->method === null) {
            $method = $this->server('request_method');

            if ($method === 'POST') {
                $xhmo = $this->headers->get('X-HTTP-Method-Override');

                if ($xhmo && in_array($xhmo, array('PUT', 'DELETE', 'PATCH'))) {
                    $method = $xhmo;
                }
            }
        } else {
            $method = $this->method;
        }

        return $method ?: $default;
    }


    public function isMethod($method)
    {
        return $this->method() === $method;
    }


    public function ajax(): Bool
    {
        return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
    }

    public function ip()
    {
        return $this->app('http')->ip();
    }

    public function url()
    {
        return $this->app('url')->request();
    }


    public function controller($method = null)
    {
        if ($method === null) {
            return defined('CONTROLLER') ? CONTROLLER : false;
        }

        return defined('ACTION') ? ACTION : false;
    }


    public function validate(array $roles)
    {
        $validation = $this->app('validator')->make($this->all(), $roles);

        if (!$validation->check()) {
            $this->app('redirect')->back()->withErrors($validation->messages());

            $this->app('response')->send();
        }
    }

    public function app($class = null)
    {
        if ($class) {
            return $this->application::get($class);
        }
        return $this->application;
    }

    public function __get($name)
    {
        return $this->request->get($name);
    }

    public function __isset($name)
    {
        return true;
    }

    public function __set($name, $value)
    {
        return $this->request->set($name, $value);
    }


    public function __call($method, $args)
    {
        return $this->request->{$method}(...$args);
    }


    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->request[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->request[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->request->remove($offset);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->request->has($offset);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->request);
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->request->all());
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unSerialize($serialized)
    {
        unserialize($serialized);
    }
}
