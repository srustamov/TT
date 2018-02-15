<?php namespace System\Libaries\Cache\Drivers;

use System\Libaries\Cache\CacheStore;


class RedisStore implements CacheStore
{


    private $put = false;

    private $key;

    private $expires;

    private $redis;

    private static $config;



    function __construct ()
    {
        if (is_null(self::$config))
        {
            self::$config = config('cache.redis');
        }

        $this->redis = new \Redis();

        try
        {
            $this->redis->connect(self::$config['host'],self::$config['port']);
        }
        catch (\RedisException $e)
        {
            throw new \Exception('Redis message: '.$e->getMessage());
        }
    }

    public function put ( String $key , $value , $expires = null )
    {

        $this->put = true;

        $this->key = $key;

        if (!is_null($expires))
        {
          $this->redis->setex($key , $expires, $value);
        }
        else
        {
          $this->redis->setex($key , 10, $value);
        }

        return $this;
    }

    public function forever ( String $key , $value )
    {
        return $this->put($key , $value ,time());
    }

    public function has ( $key )
    {
        return $this->redis->exists($key);
    }

    public function get ( $key )
    {
        return $this->redis->get($key);
    }

    public function forget ( $key )
    {
        return $this->redis->delete($key);
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
        $this->redis->flushAll();
    }


    public function __destruct ()
    {
        if ($this->put && !is_null($this->expires))
        {
            $this->redis->expire($this->key,$this->expires);
        }

        $this->redis->close();
    }

}
