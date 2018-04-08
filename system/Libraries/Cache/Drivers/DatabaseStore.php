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
        $this->table = Load::class('config')->get('cache.database',['table' => 'cache'])['table'];

        $this->gc();
    }


    public function put ( String $key , $value , $expires = null ,$forever = false)
    {

        if(is_null($expires))
        {

          if(is_null($this->expires) && !$forever)
          {
            $this->put = true;

            $this->key = $key;

            DB::pdo("REPLACE INTO $this->table SET cache_key= '$key',cache_value='$value'");
          }
          else
          {
            $expires = time() + $this->expires;

            DB::pdo("REPLACE INTO $this->table SET cache_key='$key',cache_value='$value', expires=".($forever ? 0 : $expires));

            $this->expires = null;
          }

        }
        else
        {
          DB::pdo("REPLACE INTO $this->table SET cache_key='$key',cache_value='$value' ,expires=$expires");

          $this->expires = null;
        }

        return $this;
    }

    public function forever ( String $key , $value )
    {
        return $this->put($key , $value , null, true);
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
        if($this->put && !is_null($this->key))
        {
            DB::table($this->table)->where('cache_key',$this->key)->update(['expires'=> time() + $expires]);

            $this->put = false;

            $this->key = null;
        }
        else
        {
            $this->expires = $expires;
        }

        return $this;
    }


    public function minutes ( Int $minutes )
    {
      return $this->expires($minutes * 60);
    }


    public function hours ( Int $hours )
    {
      return $this->expires($hours * 3600);
    }


    public function day ( Int $day )
    {
      return $this->expires($day * 3600 * 24);
    }


    public function flush ()
    {
        DB::table($this->table)->truncate();
    }

    private function gc()
    {
        DB::pdo("DELETE FROM {$this->table} WHERE expires < ".time()." AND expires != 0");
    }


    public function __call($method,$args)
    {
        throw new CacheDatabaseStoreException("Call to undefined method Cache::$method()");
    }



    public function close ()
    {
        return true;
    }

}
