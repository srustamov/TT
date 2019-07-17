<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use ArrayAccess;
use Countable;
use Serializable;
use JsonSerializable;
use System\Engine\App;
use System\Engine\Load;
use System\Facades\Auth;
use System\Facades\Redirect;
use System\Facades\Response;
use System\Facades\Validator;

class Request implements ArrayAccess, Countable, Serializable, JsonSerializable
{
    public $request = [];

    public $query = [];

    public $files = [];

    public $cookies = [];

    public $server = [];

    public $headers = [];

    public $routeParams = [];

    public $method;

    private $application;



    public function __construct(App $application)
    {
        $this->application = $application;

        $this->prepare();
    }


    public function prepare()
    {
        $this->server  = new Parameters($_SERVER);

        $this->headers = new Parameters(getallheaders());

        $this->cookies = new Parameters($_COOKIE);

        $this->files   = new UploadedFile($_FILES);
        
        $this->method  = $this->method('GET');

        if (0 === strpos($this->headers->get('Content-Type'), 'application/x-www-form-urlencoded')
            && \in_array(strtoupper($this->method), ['PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str(\file_get_contents('php://input'), $data);

            $this->request = new Parameters($this->trim($data));
        }
        else 
        {
            $this->query   = new Parameters($this->trim($_GET));

            $this->request = new Parameters($this->trim($_POST));
        }

        return $this;

    } 


    protected function trim($data)
    {
        $data = array_map(function ($item) {
            if (is_array($item)) {
                return $this->trim($item);
            } else {
                return trim($item);
            }
        }, $data);

        return $data;
    }


    public function setRouteParams($key,$value = null)
    {
        if(is_array($key)) {
            foreach($key as $name => $value)
            {
                $this->routeParams[$name] = $value;
            }
        } 
        else 
        {
            $this->routeParams[$key] = $value;
        }
    }

    public function params($key)
    {
        return $this->routeParams[$key] ?? false;
    }


    public function session($key = null)
    {
        if (is_null($key)) {
            return Load::class('session');
        } else {
            return Load::class('session')->get($key);
        }
    }


    public function cookie($key = null)
    {
        if($key === null) {
            return $this->cookies;
        } else {
            return $this->cookies->get($key);
        }
        
    }


    public function user()
    {
        return Load::class('auth')->user();
    }


    public function server($key = null, $default = null)
    {
        if($key === null) {
            return $this->server;
        }
        return $this->server->get(strtoupper($key),$default);
    }


    public function file($name)
    {
        $this->files->get($name);
    }



    public function method($default = 'GET'): String
    {
        
        if($this->method == null)
        {
            $method = $this->server('request_method');
            
            if ($method == 'POST') {

                $xhmo = $this->headers->get('X-HTTP-Method-Override');

                if ($xhmo && in_array($xhmo, array( 'PUT' , 'DELETE' , 'PATCH' ))) {
                    $method = $xhmo;
                }
            }
        } else {
            $method = $this->method;
        }

        return $method ? : $default;
    }


    public function isMethod($method)
    {
        return $this->method() === $method;
    }


    public function ajax(): Bool
    {
        return ($this->server('HTTP_X_REQUESTED_WITH')  === 'XMLHttpRequest');
    }


    public function controller($method = null)
    {
        if (is_null($method)) {
            return defined('CONTROLLER') ? CONTROLLER : false;
        } else {
            return defined('ACTION') ? ACTION : false;
        }
    }



    public function validate(array $roles)
    {
        $validation =  Validator::make($this->all(), $roles);

        if (!$validation->check()) {

            Redirect::back()->withErrors($validation->messages());

            Response::send();
        }
    }

    public function app()
    {
        return $this->application;
    }

    public function __get($name)
    {
        return $this->request->get($name);
    }

    public function __set($name,$value)
    {
        return $this->request->set($name,$value);
    }


    public function __call($method,$args)
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

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return json_encode($this->request->all());
    }
}
