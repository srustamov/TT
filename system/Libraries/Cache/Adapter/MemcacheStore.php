<?php namespace System\Libraries\Cache\Adapter;

use System\Engine\App;

class MemcacheStore implements CacheStoreInterface
{
    private $put = false;

    private $key;

    private $expires = 10;

    private $memcache;


    public function __construct()
    {
        $config = App::get('config')->get('cache.memcache');

        if (class_exists('\\Memcache')) {
            $this->memcache = new \Memcache;
        } elseif (class_exists('\\Memcached')) {
            $this->memcache = new \Memcached;
        } elseif (function_exists('memcache_connect')) {
            $this->memcache = memcache_connect($config['host'], $config['port']);
        } else {
            throw new \Exception("Class Memcache (Memcached) not found");
        }

        if ($this->memcache === null) {
            $this->memcache->addServer($config['host'], $config['port']);
        }
    }



    public function put(String $key, $value, $expires = null, $forever = false)
    {
        if (is_null($expires)) {
            if (is_null($this->expires)) {
                $this->put = true;

                $this->key = $key;

                $this->memcache->set($key, $value, null, 10);
            } else {
                $this->memcache->set($key, $value, null, $this->expires);

                $this->expires = null;
            }
        } else {
            $this->memcache->set($key, $value, null, $expires);

            $this->expires = null;
        }

        return $this;
    }

    public function forever(String $key, $value)
    {
        return $this->day(30)->put($key, $value);
    }

    public function has($key)
    {
        return $this->memcache->get($key) ? true : false;
    }

    public function get($key)
    {
        return $this->memcache->get($key);
    }

    public function forget($key)
    {
        return $this->memcache->delete($key);
    }

    public function expires(Int $expires)
    {
        if ($this->put && !is_null($this->key)) {
            $this->memcache->set($this->key, $this->memcache->get($this->key), null, $expires);

            $this->put = false;

            $this->key = null;
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
        $this->memcache->flush();
    }

    public function __get($key)
    {
        return $this->memcache->get($key);
    }


    public function __call($method, $args)
    {
        return $this->memcache->$method(...$args);
    }


    public function close()
    {
        $this->memcache->close();
    }
}
