<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Cookie
 */



use System\Engine\App;
use System\Facades\OpenSsl;
use System\Exceptions\CookieException;
use ArrayAccess;

class Cookie implements ArrayAccess
{
    private $prefix;

    private $http_only = true;

    private $secure = false;

    private $path;

    private $domain;

    private $encrypt_except_keys = [];



    public function __construct()
    {
        $config = App::get('config')->get('cookie');

        $this->prefix    = $config['prefix'];
        $this->http_only = is_bool($config['http_only'])? $config['http_only'] : $this->http_only;
        $this->secure    = is_bool($config['secure'])   ? $config['secure']    : $this->secure;
        $this->path      = $config['path'];
        $this->domain    = $config['domain'];
        $this->encrypt_except_keys = $config['encrypt_except_keys'] ?? [];

        unset($config);
    }


    /**
     * @param bool $http
     * @return $this|bool
     */
    public function http_only(Bool $http = true): Cookie
    {
        $this->http_only = $http;

        return $this;
    }


    /**
     * @param string $path
     * @return Cookie
     */
    public function path(String $path): Cookie
    {
        $this->path = $path;
        return $this;
    }


    /**
     * @param bool $domain
     * @return $this|bool
     */
    public function domain($domain): Cookie
    {
        $this->domain = $domain;

        return $this;
    }


    /**
     * @param Bool $bool
     * @return Cookie
     */
    public function secure(Bool $bool): Cookie
    {
        $this->secure = $bool;

        return $this;
    }


    /**
     * Flush $_COOKIE variable
     * @throws CookieException
     */
    public function flush()
    {
        foreach (array_keys($_COOKIE) as $key) {
            $this->forget($key);
        }
    }


    /**
     * @param $key
     * @throws CookieException
     */
    public function forget($key)
    {
        $this->set($key, '', -100);
    }


    /**
     * @param $key
     * @param $value
     * @param float|int $time
     * @return mixed
     * @throws CookieException
     */
    public function set($key, $value, $time = 24 * 3600)
    {
        if ($value instanceof \Closure) {
            return $this->set($key, $value($this), $time);
        }

        if (!empty(trim($value))) {
            $value = $this->encrypt($value, $key);
        }

        $data = [
          $this->prefix.$key,
          $value,
          time() + $time,
          $this->path,
          $this->domain,
          $this->secure,
          $this->http_only
        ];

        if (!setcookie(...$data)) {
            throw new CookieException('Could not set the cookie!');
        }

        return $this;
    }


    /**
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        return isset($_COOKIE[ $this->prefix . $key ]);
    }

    /**
     * @param $key
     * @return bool|mixed
     */
    public function get($key)
    {
        if ($key instanceof \Closure) {
            return $this->get($key($this));
        }

        if ($this->has($key)) {
            return $this->decrypt($_COOKIE[ $this->prefix . $key ], $key);
        }
        return false;
    }



    private function encrypt($data, $key)
    {
        if (in_array($this->prefix . $key, $this->encrypt_except_keys, true)) {
            return $data;
        }

        return OpenSsl::encrypt(serialize($data));
    }


    private function decrypt($data, $key)
    {
        if (in_array($this->prefix . $key, $this->encrypt_except_keys, true)) {
            return $data;
        }

        return unserialize(OpenSsl::decrypt($data),['allow_classes' => []]);
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
     * @throws CookieException
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws CookieException
     */
    public function offsetUnset($offset)
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
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
}
