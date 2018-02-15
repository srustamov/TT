<?php namespace System\Libaries\Cache\Drivers;

use System\Libaries\Cache\CacheStore;

class DatabaseStore implements CacheStore
{

    private $put = false;

    private $key;

    private $expires;

    private static $db;

    private static $table;


    function  __construct ()
    {
        if (is_null(self::$db))
        {
            $config = config('cache.database');

            self::$table = $config['table'];

            self::$db = (new \System\Libraries\Database\Database())->pdo();

        }

        $this->gc();
    }

    public function createDatabaseTable()
    {

        try
        {
            self::$db->exec("CREATE TABLE IF NOT EXISTS `".self::$table."` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `cache_key`  varchar(255) NOT NULL,
                      `cache_value` longtext NOT NULL,
                      `expires` int(20) NOT NULL DEFAULT '0',
                       PRIMARY KEY (`id`),
                       UNIQUE KEY `cache_key` (`cache_key`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
        catch (\PDOException $e)
        {
            echo '<div style="padding: 10px;border:1px solid red">'.$e->getMessage().'</div>';
        }
    }

    public function put ( String $key , $value , $expires = 10 )
    {
        $this->put = true;

        $this->key = $key;

        try
        {

            $insert = self::$db->prepare("REPLACE INTO ".self::$table." SET cache_key=?,cache_value=?,expires=?");

            $insert->execute(array($key,$value,time()+$expires));

        }catch (\PDOException $e)
        {
            echo $e->getMessage();
        }

        return $this;
    }

    public function forever ( String $key , $value )
    {
        return $this->put($key , $value ,time());
    }

    public function has ( $key ):Bool
    {
        $has = self::$db->query("SELECT cache_key FROM ".self::$table." WHERE cache_key={$key}");

        return $has->rowCount() > 0;
    }

    public function get ( $key )
    {
        $data = self::$db->query("SELECT cache_key FROM ".self::$table." WHERE cache_key={$key}");

        if($data->rowCount() > 0)
        {
          return $data->fetch();
        }
        else
        {
          return false;
        }
    }

    public function forget ( $key )
    {
        self::$db->query("DELETE FROM ".self::$table." WHERE cache_key='{$key}'");
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
        self::$db->query("DELETE FROM ".self::$table);
    }

    private function gc()
    {
      self::$db->query("DELETE FROM ".self::$table." WHERE expires < ".time());
    }

    function __destruct ()
    {
        if ($this->put && !is_null($this->expires))
        {
          $update = self::$db->prepare("UPDATE ".self::$table." SET expires=? WHERE cache_key=?");
          $update->execute(array( time() + $this->expires,$this->key));
        }
    }
}
