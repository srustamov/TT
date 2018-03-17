<?php namespace System\Libraries\Cache;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Libraries
 * @category    Cache
 */


use System\Libraries\Cache\Drivers\FileStore;
use System\Libraries\Cache\Drivers\DatabaseStore;
use System\Libraries\Cache\Drivers\MemcacheStore;
use System\Libraries\Cache\Drivers\RedisStore;
use System\Facades\Load;

class Cache implements CacheStore
{


    private $driver;


    function  __construct ()
    {

        $driver = Load::config('cache.driver','file');

        $this->driver($driver);
        
    }

    

    public function driver($driver)
    {
        if($driver instanceof CacheStore)
        {
            $this->driver = $driver;
        }
        else
        {
            switch (strtolower($driver))
            {
                case 'file':
                    $this->driver = new FileStore();
                    break;
                case 'database':
                    $this->driver = new DatabaseStore();
                    break;
                case 'memcache':
                    $this->driver = new MemcacheStore();
                    break;
                case 'redis':
                    $this->driver = new RedisStore();
                    break;
                default:
                    $this->driver = new FileStore();
                    break;
            }
        }
        return new static();
    }


    public function put(String $key , $value ,$expires = 10)
    {
        return $this->driver->put($key , $value ,$expires);
    }


    public function forever(String $key , $value )
    {
        return $this->driver->forever($key , $value);
    }


    public function has($key)
    {
        return $this->driver->has($key);
    }


    public function get($key)
    {
        return $this->driver->get($key);
    }


    public function forget($key)
    {
        return $this->driver->forget($key);
    }


    public function expires(Int $expires)
    {
        return $this->driver->expires($expires);
    }


    public function minutes(Int $minutes)
    {
        return $this->driver->minutes($minutes);
    }

    public function flush ()
    {
        $this->driver->flush();
    }

    public function __call($method,$args)
    {
      return $this->driver->$method(...$args);
    }




}
