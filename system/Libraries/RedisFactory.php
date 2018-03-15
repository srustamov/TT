<?php namespace System\Libraries;

class RedisFactory
{

    private $config;

    private $redis;


    function __construct()
    {

        $this->config = config('cache.redis');

        $this->redis = new \Redis();

        try
        {
            $this->redis->connect($this->config['host'], $this->config['port']);
        }
        catch (\RedisException $e)
        {
            throw new \RuntimeException('Redis message: '.$e->getMessage());
        }

    }


    public function connection()
    {
        if(is_null($this->redis)) {
            $this->__construct();
        }
        return $this->redis;
    }



    public function __call ( $name , $arguments )
    {
       return $this->redis->$name(...$arguments);
    }
}
