<?php namespace System\Libraries\Cache\Drivers;




use System\Facades\Redis as DRedis;

class RedisStore implements CacheStore
{

    private $key;

    private $put;

    private $expires;

    public function put ( String $key , $value , $expires = null )
    {

        $this->put = true;

        $this->key = $key;

        if(is_null($expires))
        {
            $expires = $this->expires;
        }

        if(is_null($expires))
        {
            DRedis::set($key ,$value);
        }
        else
        {
            DRedis::setex($key , $expires, $value);
        }

        return $this;
    }

    public function forever ( String $key , $value )
    {
        return $this->put($key , $value ,time());
    }

    public function has ( $key )
    {
        return DRedis::exists($key);
    }

    public function get ( $key )
    {
        return DRedis::get($key);
    }

    public function forget ( $key )
    {
        DRedis::delete($key);
    }

    public function expires ( Int $expires )
    {
        if(!is_null($this->put))
        {
            DRedis::expire($this->key,$expires);
        }
        else
        {
            $this->expires = $expires;
        }

        return $this;
    }

    public function minutes ( Int $minutes )
    {
        if(!is_null($this->put))
        {
            DRedis::expire($this->key,$minutes*60);
        }
        else
        {
            $this->expires = $minutes*60;
        }

        return $this;
    }

    public function flush()
    {
        DRedis::flushAll();
    }

    public function __get ( $key )
    {
        return DRedis::get($key);
    }


    public function __call($method,$args)
    {
      return DRedis::$method(...$args);
    }

    public function __destruct ()
    {
        DRedis::close();
    }

}
