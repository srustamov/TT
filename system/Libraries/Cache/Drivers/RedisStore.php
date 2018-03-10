<?php namespace System\Libraries\Cache\Drivers;



use System\Libraries\Cache\CacheStore;
use System\Libraries\RedisFactory;

class RedisStore implements CacheStore
{

    private $redis;

    function __construct ()
    {
        $this->redis = new RedisFactory();
    }

    public function put ( String $key , $value , $expires = null )
    {

        $this->put = true;

        $this->key = $key;

        $this->redis->setex($key , $expires, $value);

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
        $this->redis->delete($key);
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

    public function __get ( $key )
    {
        return $this->redis->get($key);
    }


    public function __call($method,$args)
    {
      return $this->redis->$method(...$args);
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
