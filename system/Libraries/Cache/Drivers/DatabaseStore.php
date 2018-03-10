<?php namespace System\Libraries\Cache\Drivers;

use System\Libraries\Cache\CacheStore;

class DatabaseStore implements CacheStore
{

    private $put = false;

    private $key;

    private $expires;

    private static $table;


    function  __construct ()
    {
        if (is_null(self::$table))
        {
            self::$table = config('cache.database',['table' => 'cache'])['table'];
        }

        $this->gc();
    }


    public function put ( String $key , $value , $expires = 10 )
    {
        $this->put = true;

        $this->key = $key;

        app('db')->table(self::$table)->set([
            'cache_key' => $key,
            'cache_value' => $value,
            'expires' => time()+$expires
        ])->insert();

        return $this;
    }

    public function forever ( String $key , $value )
    {
        return $this->put($key , $value ,time());
    }

    public function has ( $key ):Bool
    {
        return (bool) app('db')->table(self::$table)->where('cache_key',$key)->first();
    }

    public function get ( $key )
    {
        return app('db')->table(self::$table)->where('cache_key',$key)->first();
    }

    public function forget ( $key )
    {
        return app('db')->table(self::$table)->where('cache_key',$key)->delete();
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

    public function flush ()
    {
        app('db')->table(self::$table)->delete();
    }

    private function gc()
    {
        app('db')->table(self::$table)->where('expires','<',time())->delete();
    }

    
    public function __call($method,$args)
    {
      throw new CacheDatabaseStoreException("Call to undefined method Cache::$method()");
    }


    function __destruct ()
    {
        if ($this->put && !is_null($this->expires))
        {
            app('db')->table(self::$table)->set([
                'expires' => time() + $this->expires
            ])->where('cache_key',$this->key)->update();
        }
    }
}
