<?php namespace System\Libraries;

use System\Engine\Load;
use Predis\Client as RedisDriver;

class Redis extends RedisDriver
{
    private $redis;


    public function __construct($option = [])
    {
        if (!empty($option)) {
            $this->redis = parent::__construct($option);
        } else {
            $config =  Load::class('config')->get('cache.redis');
            $this->redis = parent::__construct($config);
        }

        return $this->redis;
    }


    public function connection()
    {
        if ($this->redis === null) {
            $this->__construct();
        }
        return $this->redis;
    }
}
