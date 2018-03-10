<?php namespace System\Libraries\Cache\Drivers;


use System\Libraries\Cache\CacheStore;

class MemcacheStore implements CacheStore
{

    private $put = false;

    private $key;

    private $expires = 10;

    private $memcache;

    private static $config;

    function __construct ()
    {
        if (is_null(self::$config))
        {
            self::$config = config('cache.memcache');
        }

        $this->memcache = new \Memcache;

        $this->memcache->addServer(self::$config['host'],self::$config['port']);
    }

    public function put ( String $key , $value , $expires = 10 )
    {
        $this->put = true;

        $this->key = $key;

        $this->memcache->set($key , $value , null ,$expires);

        return $this;
    }

    public function forever ( String $key , $value )
    {
        return $this->put($key , $value ,time());
    }

    public function has ( $key )
    {
        return $this->memcache->get($key) ? true : false;
    }

    public function get ( $key )
    {
        return $this->memcache->get($key);
    }

    public function forget ( $key )
    {
        return $this->memcache->delete($key);
    }

    public function expires ( Int $expires )
    {
        $this->expires = $expires;
        return $this;
    }

    public function minutes ( Int $minutes )
    {
        $this->expires = $minutes*60;
        return $this;
    }

    public function flush()
    {
        $this->memcache->flush();
    }

    public function __get ( $key )
    {
        return $this->memcache->get($key);
    }


    public function __call($method,$args)
    {
      return $this->memcache->$method(...$args);
    }

    public function __destruct ()
    {
        if ($this->put)
        {
            $this->memcache->set($this->key,$this->memcache->get($this->key),null,$this->expires);
        }

        $this->memcache->close();
    }

}
