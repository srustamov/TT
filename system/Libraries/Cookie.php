<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Libraries
 * @category    Cookie
 */





class Cookie
{

    const ENC_KEY = "1xo86bFafRcUx8IccN6mdFflstIkcmJiY+li7Qi7hWScfJS2StKBmwnff4378";

    private static $config;

    private $prefix = '';

    private $http_only = true;

    private $secure = false;

    private $path = '/';

    private $domain = '';


    /**
     * Cookie constructor.
     */
    public function __construct(...$args)
    {
        if(is_null(static::$config))
        {
          static::$config = config('cookie');
        }

        $config = static::$config;

        $this->prefix    = !empty($config['prefix'])    ? $config['prefix']    : $this->prefix;
        $this->http_only = is_bool($config['http_only'])? $config['http_only'] : $this->http_only;
        $this->secure    = is_bool($config['secure'])   ? $config['secure']    : $this->secure;
        $this->path      = !empty($config['path'])      ? $config['path']      : $this->path;
        $this->domain    = !empty($config['domain'])    ? $config['domain']    : $this->domain;

        if(!empty($args)) {
          $this->set(...$args);
        }
    }


    /**
     * @param bool $http
     * @return $this|bool
     */
    public function http_only($http = true)
    {
        $this->http_only = (bool) $http;

        return $this;
    }


    /**
     * @param string $path
     * @return bool|Cookie
     */
    public function path($path = '/')
    {
        $this->path = $path;
        return $this;
    }


    /**
     * @param bool $domain
     * @return $this|bool
     */
    public function domain($domain = '')
    {
        $this->domain = $domain ?? '';
        return $this;
    }


    /**
     * @param bool $bool
     */
    public function secure($bool = false)
    {
        if (!is_bool($bool))
        {
            return false;
        }

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
            $value = $this->encrypt(json_encode($value));
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
            throw new \Exception("Could not set the cookie!");
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
                return json_decode($this->decrypt($_COOKIE[ $this->prefix . $key ]));
            }
            return false;
        }
    }



    private function encrypt($data)
    {
      $encrypt_key = config('config.encryption_key', self::ENC_KEY);

      $encrypted_data = openssl_encrypt(
        $data, "AES-256-CBC", mb_substr($encrypt_key,0,32),OPENSSL_RAW_DATA,mb_substr($encrypt_key,0,16)
      );
      return base64_encode ( $encrypted_data );
    }


    private function decrypt($data)
    {
      $encrypt_key = config('config.encryption_key', self::ENC_KEY);

      $decrypted_data = openssl_decrypt(
        base64_decode($data), "AES-256-CBC", mb_substr($encrypt_key,0,32),OPENSSL_RAW_DATA,mb_substr($encrypt_key,0,16)
      );
      return $decrypted_data;
    }
}
