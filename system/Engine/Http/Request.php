<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use ArrayAccess;
use Countable;
use Serializable;
use JsonSerializable;
use System\Facades\Validator;


class Request implements ArrayAccess ,Countable,Serializable,JsonSerializable
{


    private $request;

    private $request_method;

    public function __construct()
    {
        $this->request_method = $this->server('request_method');

        $this->request = array(
        'GET'    => $this->trim($_GET),
        'POST'   => $this->trim($_POST),
        'REQUEST'=> $this->trim($_REQUEST),
      );
    }


    private function trim($data)
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




    public function __get($key)
    {
        return $this->request[ $this->request_method ][ $key ] ?? false;
    }


    public function __set($key, $value)
    {
        $this->request[ $this->request_method ][ $key ] = $value;
    }


    public function all()
    {
        $data = $this->request[ $this->request_method ];

        if (isset($data[ '_token' ])) {
            unset($data[ '_token' ]);
        }

        return $data;
    }


    public function params($key)
    {
      return $this->request['REQUEST'][$key] ?? false;
    }


    public function session($key = null)
    {
        if (is_null($key)) {
          return app('session');
        } else {
          return app('session')->get($key);
        }
    }


    public function cookie($key)
    {
        return app('cookie')->get($key);
    }


    public function server($key)
    {
        return $_SERVER[ strtoupper($key) ] ?? false;
    }


    public function input($name = null)
    {
        if (!is_null($name)) {
            return app('input')->{$this->method()}($name);
        } else {
            return app('input');
        }
    }


    public function file($name)
    {
        if (isset($_FILES[ $name ])) {
            if ($_FILES[ $name ][ 'error' ] > 0) {
                return false;
            } else {
                return $_FILES[ $name ];
            }
        } else {
            return false;
        }
    }


    public function post($name)
    {
        if (isset($this->request['POST'][$name])) {
            if (!empty($this->request['POST'][$name])) {
                return $this->request['POST'][$name];
            }
        }
        return false;
    }


    public function get($name)
    {
        if (isset($this->request['GET'][$name])) {
            if (!empty($this->request['GET'][$name])) {
                return $this->request['GET'][$name];
            }
        }
        return false;
    }


    public function method(): String
    {
        $method = $this->server('request_method');

        if ($method == 'POST') {
            $headers = getallheaders();

            $xhmo    = $headers[ 'X-HTTP-Method-Override' ] ?? false;

            if ($xhmo && in_array($xhmo, array( 'PUT' , 'DELETE' , 'PATCH' ))) {
                $method = $xhmo;
            }
        }
        return $method;
    }


    public function ajax(): String
    {
        return app('http')->isAjax();
    }


    public function controller()
    {
        return $this->server('called_controller');
    }


    public function action()
    {
        return $this->server('called_method');
    }


    public function only(): array
    {
        $_data = [];

        $all   = $this->all();

        $args  = func_get_args();

        if(isset($args[0])) {
          if(is_array($args[0])) {
            $args = $args[0];
          }
        }

        foreach ($args as $key) {
            $_data[ $key ] = $all[$key] ?? false;
        }

        return $_data;
    }


    public function except()
    {
        $data = $this->all();


        $args  = func_get_args();

        if(isset($args[0])) {
          if(is_array($args[0])) {
            $args = $args[0];
          }
        }

        foreach ($args as $key) {
            if (isset($data[$key])) {
                unset($data[$key]);
            }
        }

        return $data;
    }


    public function validate(array $roles)
    {
        $validation =  Validator::make($this->all(), $roles);

        if(!$validation->check()) {
          return redirect()->back()->withErrors($validation->messages());
        }
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
    public function offsetGet ( $offset )
    {
        return $this->{$offset};
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
    public function offsetSet ( $offset , $value )
    {
        $this->{$offset} = $value;
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
    public function offsetUnset ( $offset )
    {
        if (isset( $this->request[ $this->request_method ][ $offset ] )) {
            unset($this->request[ $this->request_method ][ $offset ]);
        }
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
    public function offsetExists ( $offset )
    {
        return isset( $this->request[ $this->request_method ][ $offset ] );
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
    public function count ()
    {
        return count($this->request[ $this->request_method ]);
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize ()
    {
        return serialize($this->request[ $this->request_method ]);
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
    public function unSerialize ( $serialized )
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
    function jsonSerialize ()
    {
        return json_encode($this->request[ $this->request_method ]);
    }
}
