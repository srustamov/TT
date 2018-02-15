<?php namespace System\Libararies\Cache;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Libraries
 * @category    Cache
 */



use System\Libraries\Cache\CacheStore;
use System\Libraries\Cache\Drivers\FileStore;
use System\Libraries\Cache\Drivers\DatabaseStore;
use System\Libraries\Cache\Drivers\MemcacheStore;
use System\Libraries\Cache\Drivers\RedisStore;




class Cache implements CacheStore
{


    private static $driver;


    function  __construct ()
    {
        if (is_null(self::$driver))
        {
            $driver = tt_config('cache.driver','file');

            self::driver($driver);
        }
    }


    public static function driver($driver)
    {

        if ($driver instanceof CacheStore)
        {
          self::$driver = $driver;
        }
        else
        {
          switch (strtolower($driver))
          {
              case 'file':
                  self::$driver = new FileStore();
                  break;
              case 'database':
                  self::$driver = new DatabaseStore();
                  break;
              case 'memcache':
                  self::$driver = new MemcacheStore();
                  break;
              case 'redis':
                  self::$driver = new RedisStore();
                  break;
              default:
                  self::$driver = new FileStore();
                  break;
          }

        }
        return new static();
    }


    public function put(String $key , $value ,$expires = 10)
    {
        if (!is_string($value) && is_callable($value))
        {
            $value = call_user_func($value,$this);
        }
        return self::$driver->put($key , $value ,$expires);
    }



    public function forever(String $key , $value )
    {
        return self::$driver->forever($key , $value);
    }




    public function has($key)
    {
        if (!is_string($key) && is_callable($key))
        {
            $key = call_user_func($key,$this);
        }
        return self::$driver->has($key);
    }



    public function get($key)
    {
        if (!is_string($key) && is_callable($key))
        {
            $key = call_user_func($key,$this);
        }
        return self::$driver->get($key);
    }



    public function forget($key)
    {
        if (!is_string($key) && is_callable($key))
        {
            $key = call_user_func($key,$this);
        }
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



    public function createDatabaseTable()
    {
        (new DatabaseStore())->createDatabaseTable();
    }


    public function __get($key)
    {
        return self::$driver->get($key);
    }



    function __destruct()
    {
      //self::$driver->__destruct();
    }





}
