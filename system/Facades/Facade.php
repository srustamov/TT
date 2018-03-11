<?php namespace System\Facades;



use System\Core\Load;

abstract class Facade
{


  protected static $load;



  protected static function getFacadeAccessor()
  {
      throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
  }


  protected static function resolveFacadeInstance($name)
  {
      return static::getLoadAccessor()->class($name);
  }


  public static function getFacadeRoot()
  {
      return static::resolveFacadeInstance(static::getFacadeAccessor());
  }


  public static function __callStatic($method, $args)
  {
      $instance = static::getFacadeRoot();

      if (! $instance) {
          throw new \RuntimeException('A facade root has not been set.');
      }

      return $instance->$method(...$args);
  }


  protected static function getLoadAccessor()
  {
    if(is_null(static::$load)) {
      static::$load = new Load;
    }
    return static::$load;
  }
}
