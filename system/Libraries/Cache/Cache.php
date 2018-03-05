<?php namespace System\Libraries\Cache;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Libraries
 * @category    Cache
 */

use System\Libraries\Cache\CacheStore;


class Cache implements CacheStore
{


    private static $driver;


    function  __construct ()
    {
        if (is_null(self::$driver))
        {
            $driver = config('cache.driver','file');

            $this->driver($driver);
        }
    }


    public static function driver($driver)
    {
        if($driver instanceof CacheStore)
        {
            self::$driver = $driver;
        }
        else
        {
            switch (strtolower($driver))
            {
                case 'file':
                    self::$driver = new \System\Libraries\Cache\Drivers\FileStore();
                    break;
                case 'database':
                    self::$driver = new \System\Libraries\Cache\Drivers\DatabaseStore();
                    break;
                case 'memcache':
                    self::$driver = new \System\Libraries\Cache\Drivers\MemcacheStore();
                    break;
                case 'redis':
                    self::$driver = new \System\Libraries\Cache\Drivers\RedisStore();
                    break;
                default:
                    self::$driver = new \System\Libraries\Cache\Drivers\FileStore();
                    break;
            }
        }
        return new static();
    }


    public function put(String $key , $value ,$expires = 10)
    {
        return self::$driver->put($key , $value ,$expires);
    }


    public function forever(String $key , $value )
    {
        return self::$driver->forever($key , $value);
    }


    public function has($key)
    {
        return self::$driver->has($key);
    }


    public function get($key)
    {
        return self::$driver->get($key);
    }


    public function forget($key)
    {
        return self::$driver->forget($key);
    }


    public function expires(Int $expires)
    {
        return self::$driver->expires($expires);
    }


    public function minutes(Int $minutes)
    {
        return self::$driver->minutes($minutes);
    }
    
    public function flush ()
    {
        self::$driver->flush();
    }



    function __destruct()
    {
    }







}
