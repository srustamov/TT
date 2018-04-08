<?php namespace System\Libraries;

use System\Facades\Load;

class RedisFactory
{

    private $redis;


    function __construct()
    {
        $this->connection();
    }


    public function connection()
    {
        if (is_null($this->redis))
        {
          $config = Load::class('config')->get('cache.redis');

          $this->redis = new \Redis();

          try
          {
              $this->redis->connect($config['host'], $config['port']);
          }
          catch (\RedisException $e)
          {
              throw new \RuntimeException('Redis message: '.$e->getMessage());
          }

        }

        return $this->redis;
    }



    public function __call ( $name , $arguments )
    {
       return $this->redis->$name(...$arguments);
    }
}
