<?php namespace System\Libraries\Cache\Drivers;

use System\Facades\Redis as DRedis;

class RedisStore implements CacheStore
{
    private $key;

    private $put;

    private $expires;



    public function put(String $key, $value, $expires = null, $forever = false)
    {
        $this->put = true;

        $this->key = $key;

        if (is_null($expires)) {
            $expires = $this->expires;
        }

        if (is_null($expires)) {
            DRedis::set($key, $value);
        } else {
            DRedis::setex($key, $expires, $value);
        }

        return $this;
    }

    public function forever(String $key, $value)
    {
        return $this->day(30)->put($key, $value);
    }

    public function has($key)
    {
        return DRedis::exists($key);
    }

    public function get($key)
    {
        return DRedis::get($key);
    }

    public function forget($key)
    {
        DRedis::del($key);
    }

    public function expires(Int $expires)
    {
        if (!is_null($this->put)) {
            DRedis::expire($this->key, $expires);
        } else {
            $this->expires = $expires;
        }

        return $this;
    }


    public function minutes(Int $minutes)
    {
        return $this->expires($minutes * 60);
    }


    public function hours(Int $hours)
    {
        return $this->expires($hours * 3600);
    }


    public function day(Int $day)
    {
        return $this->expires($day * 3600 * 24);
    }


    public function flush()
    {
        DRedis::flushAll();
    }

    public function __get($key)
    {
        return DRedis::get($key);
    }


    public function __call($method, $args)
    {
        return DRedis::$method(...$args);
    }

    public function close()
    {
        DRedis::close();
    }
}
