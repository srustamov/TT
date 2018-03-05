<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */





class Request
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


    public function session($key)
    {
        return app('session')->get($key);
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
        if (is_null($name)) {
            return app('input')->{$this->request_method}($name);
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


    public function only(...$only): array
    {
        $_data = [];

        $all   = $this->all();

        foreach ($only as $key) {
            $_data[ $key ] = $all[$key] ?? false;
        }

        return $_data;
    }


    public function except(...$excepts)
    {
        $data = $this->all();

        foreach ($excepts as $key) {
            if (isset($data[$key])) {
                unset($data[$key]);
            }
        }

        return $data;
    }


    public function validate(array $roles): Bool
    {
        return app('validator')->make($this->all(), $roles);
    }
}
