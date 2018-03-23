<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Cookie
 */



use System\Facades\Load;
use System\Facades\OpenSsl;
use System\Exceptions\CookieException;
use ArrayAccess;

class Cookie implements ArrayAccess
{

    private $prefix = '';

    private $http_only = true;

    private $secure = false;

    private $path = '/';

    private $domain = '';



    function __construct()
    {

        $config = Load::config('cookie');

        $this->prefix    = !empty($config['prefix'])    ? $config['prefix']    : $this->prefix;
        $this->http_only = is_bool($config['http_only'])? $config['http_only'] : $this->http_only;
        $this->secure    = is_bool($config['secure'])   ? $config['secure']    : $this->secure;
        $this->path      = !empty($config['path'])      ? $config['path']      : $this->path;
        $this->domain    = !empty($config['domain'])    ? $config['domain']    : $this->domain;

        if(func_num_args() > 0)
        {
          $this->set(...func_get_args());
        }
    }


    /**
     * @param bool $http
     * @return $this|bool
     */
    public function http_only(Bool $http = true)
    {
        $this->http_only = $http;

        return $this;
    }


    /**
     * @param string $path
     * @return bool|Cookie
     */
    public function path(String $path = '/')
    {
        $this->path = $path;

        return $this;
    }


    /**
     * @param bool $domain
     * @return $this|bool
     */
    public function domain($domain = null)
    {
        $this->domain = $domain;
        
        return $this;
    }


    /**
     * @param bool $bool
     */
    public function secure(Bool $bool = true)
    {
        $this->secure = $bool;

        return $this;
    }


    /**
     * Flush $_COOKIE variable
     */
    public function flush()
    {
        $Cookies = array_keys($_COOKIE);

        foreach ($Cookies as $Cookie)
        {
            $this->forget($Cookie);
        }
    }


    /**
     * @param $Key
     */
    public function forget($key)
    {
        $this->set($key, '', -1);
    }


    /**
     * @param $key
     * @param $value
     * @param null $time
     * @throws \Exception
     */
    public function set($key, $value, $time = 3600 * 24)
    {
        if (is_callable($value))
        {
            return $this->set($key, call_user_func($value, $this), $time);
        }

        if (!empty(trim($value)))
        {
            $value = $this->encrypt(serialize($value));
        }

        $set = setcookie(
            $this->prefix . $key,
            $value,
            time() + $time,
            $this->path, $this->domain,
            $this->secure,
            $this->http_only
        );

        if (!$set)
        {
            throw new CookieException("Could not set the cookie!");
        }
    }


    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return isset($_COOKIE[ $this->prefix . $key ]);
    }

    /**
     * @param $Key
     * @return bool
     */
    public function get($key)
    {
        if (is_callable($key))
        {
            return $this->get(call_user_func($key, $this), $encode);
        }
        else
        {
            if (isset($_COOKIE[ $this->prefix . $key ]))
            {
                return unserialize($this->decrypt($_COOKIE[ $this->prefix . $key ]));
            }
            return false;
        }
    }



    private function encrypt($data)
    {
      return OpenSsl::encrypt($data);
    }


    private function decrypt($data)
    {
      return OpenSsl::decrypt($data);
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
        return $this->get($offset);
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
        $this->set($offset,$value);
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
        $this->forget($offset);
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
        return $this->has($offset);
    }


}
