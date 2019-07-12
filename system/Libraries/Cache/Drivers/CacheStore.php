<?php namespace System\Libraries\Cache\Drivers;

interface CacheStore
{
    public function put(String $key, $value, $expires = null, $forever = false);

    public function forever(String $key, $value);

    public function has($key);

    public function get($key);

    public function forget($key);

    public function expires(Int $expires);

    public function minutes(Int $minutes);

    public function hours(Int $hours);

    public function day(Int $day);

    public function flush();

    public function close();
}
