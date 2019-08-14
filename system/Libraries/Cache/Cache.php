<?php namespace System\Libraries\Cache;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Cache
 */


use System\Engine\App;
use System\Facades\DB;

class Cache
{
    /**@var Adapter\CacheStoreInterface*/
    private $adapter;


    public function __construct()
    {
        $adapter = App::get('config')->get('cache.adapter', 'file');

        $this->adapter($adapter);
    }



    public function adapter($adapter)
    {
        if ($adapter instanceof Adapter\CacheStoreInterface) {
            $this->adapter = $adapter;
        } else {
            switch (strtolower($adapter)) {
                case 'database':
                    $this->adapter = new Adapter\DatabaseStore();
                    break;
                case 'memcache':
                    $this->adapter = new Adapter\MemcacheStore();
                    break;
                case 'redis':
                    $this->adapter = new Adapter\RedisStore();
                    break;
                default:
                    $this->adapter = new Adapter\FileStore();
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
        return $this->adapter->put($key, $value, $expires);
    }


    public function forever(String $key, $value)
    {
        return $this->adapter->forever($key, $value);
    }


    public function has($key)
    {
        return $this->adapter->has($key);
    }


    public function get($key)
    {
        return $this->adapter->get($key);
    }


    public function forget($key)
    {
        return $this->adapter->forget($key);
    }


    public function expires(Int $expires)
    {
        return $this->adapter->expires($expires);
    }


    public function minutes(Int $minutes)
    {
        return $this->adapter->minutes($minutes);
    }

    public function hours(Int $hours)
    {
        return $this->adapter->hours($hours);
    }

    public function day(Int $day)
    {
        return $this->adapter->day($day);
    }

    public function flush()
    {
        $this->adapter->flush();
    }

    public function __call($method, $args)
    {
        return $this->adapter->$method(...$args);
    }


    public function __destruct()
    {
        $this->adapter->close();
    }
}
