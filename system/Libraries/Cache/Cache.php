<?php namespace System\Libraries\Cache;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Cache
 */

use System\Libraries\Cache\Drivers\CacheStore;
use System\Libraries\Cache\Drivers\FileStore;
use System\Libraries\Cache\Drivers\DatabaseStore;
use System\Libraries\Cache\Drivers\MemcacheStore;
use System\Libraries\Cache\Drivers\RedisStore;
use System\Engine\App;
use System\Facades\DB;

class Cache
{
    /**@var Drivers\CacheStore*/
    private $driver;


    public function __construct()
    {
        $driver = App::get('config')->get('cache.driver', 'file');

        $this->driver($driver);
    }



    public function driver($driver)
    {
        if ($driver instanceof CacheStore) {
            $this->driver = $driver;
        } else {
            switch (strtolower($driver)) {
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
        return $this;
    }


    public function createDatabaseTable()
    {
        try {
            $table = App::get('config')->get('cache.database', [])['table'] ?? 'cache';

            $create = DB::exec("CREATE TABLE IF NOT EXISTS $table(
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `cache_key` varchar(255) NOT NULL,
                              `cache_value` longtext NOT NULL,
                              `expires` int(20) NOT NULL DEFAULT '0',
                               PRIMARY KEY (`id`),
                               UNIQUE KEY `cache_key` (`cache_key`)
                              ) DEFAULT CHARSET=utf8
                      ") !== false;

            return $create ? $this : false;
        } catch (\PDOException $e) {
            throw new \Exception("Create database $table table failed.<br />[".$e->getMessage()."]");
        }
    }


    public function put(String $key, $value, $expires = null)
    {
        return $this->driver->put($key, $value, $expires);
    }


    public function forever(String $key, $value)
    {
        return $this->driver->forever($key, $value);
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

    public function hours(Int $hours)
    {
        return $this->driver->hours($hours);
    }

    public function day(Int $day)
    {
        return $this->driver->day($day);
    }

    public function flush()
    {
        $this->driver->flush();
    }

    public function __call($method, $args)
    {
        return $this->driver->$method(...$args);
    }


    public function __destruct()
    {
        $this->driver->close();
    }
}
