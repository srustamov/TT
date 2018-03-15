<?php namespace System\Libraries\Cache\Drivers;

use System\Facades\Load;
use System\Facades\DB;

class DatabaseStore implements CacheStore
{

    private $put = false;

    private $key;

    private $expires;

    private $table;


    function  __construct ()
    {
        $this->table = Load::config('cache.database',['table' => 'cache'])['table'];
        $this->gc();
    }


    public function put ( String $key , $value , $expires = 10 )
    {
        $this->put = true;

        $this->key = $key;

        DB::table($this->table)->set([
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
        return (bool) DB::table($this->table)->where('cache_key',$key)->first();
    }

    public function get ( $key )
    {
        return DB::table($this->table)->where('cache_key',$key)->first();
    }

    public function forget ( $key )
    {
        return DB::table($this->table)->where('cache_key',$key)->delete();
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
        DB::table($this->table)->delete();
    }

    private function gc()
    {
        DB::table($this->table)->where('expires','<',time())->delete();
    }


    public function __call($method,$args)
    {
        throw new CacheDatabaseStoreException("Call to undefined method Cache::$method()");
    }


    function __destruct ()
    {
        if ($this->put && !is_null($this->expires))
        {
            DB::table($this->table)->set([
                'expires' => time() + $this->expires
            ])->where('cache_key',$this->key)->update();
        }
    }
}
