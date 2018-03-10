<?php namespace System\Facades;





abstract class Facade
{


  protected static $instances;



  protected static function getFacadeAccessor()
  {
      throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
  }


  protected static function resolveFacadeInstance($name)
  {
      if (is_object($name)) {
          return $name;
      }

      if (isset(static::$instances[$name])) {
          return static::$instances[$name];
      }

      return static::$instances[$name] = app($name);
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
}
