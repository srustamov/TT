<?php namespace System\Engine;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


use ReflectionMethod;
use ReflectionFunction;


class Reflections
{

  public static function classMethodParameters($className,$method,array $args = [])
  {

    if(!method_exists($className,$method))
    {
      return $args;
    }

    $pParameters = (new ReflectionMethod($className,$method))->getParameters();

    foreach ($pParameters as $num => $param)
    {
        if ($param->getClass())
        {
            $class = $param->getClass()->name;

            if(($instance = Load::applicationClasses($class)))
            {
                $args[$num] = Load::class($instance);
            }
            else
            {
                $args[$num] = new $class();
            }
        }
    }
    return $args;
  }


  public static function functionParameters($function,array $args = [])
  {
      $parameters  = (new ReflectionFunction($function))->getParameters();

      foreach ($parameters as $num => $param)
      {
          if ($param->getClass()) {

              $class = $param->getClass()->name;

              if(Load::applicationClasses($class,true))
              {
                  $args[$num] = Load::class($class);
              }
              else
              {
                  $args[$num] = new $class();
              }
          }
      }
      return $args;
  }


}
