<?php namespace System\Libraries\Cache;


interface CacheStore
{
  public function put(String $key , $value ,$expires = 10);
  public function forever(String $key , $value);
  public function has($key);
  public function get($key);
  public function forget($key);
  public function expires(Int $expires);
  public function minutes(Int $minutes);
  public function flush();
}