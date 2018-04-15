<?php namespace System\Engine;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



class Load implements \ArrayAccess
{


    private static $classes = [];


    public static function class( String $class,...$args)
    {

        if(isset(static::$classes[$class]))
        {
            return static::$classes[$class];
        }
        else
        {

            if (($instance = App::instance()->classes($class)))
            {
                if (method_exists($instance,'__construct'))
                {
                    $args = Reflections::classMethodParameters($instance,'__construct',$args);
                }

                static::$classes[$class] = new $instance(...$args);

                return static::$classes[$class];
            }
            else
            {
                if(strpos($class,'\\'))
                {

                    if(($instance = App::instance()->classes($class,true)))
                    {
                        return static::class($instance,...$args);
                    }
                    else
                    {
                        $instance = new $class(...Reflections::classMethodParameters($class,'__construct', $args));

                        static::$classes[$class] = $instance;

                        unset($instance);

                        return static::$classes[$class];
                    }

                }
            }

        }
        throw new \Exception('Class not found ['.$class.']');
    }


    public static function register($className,$object)
    {
        if($object instanceof \Closure) {
            static::register($className,call_user_func($object));
        } elseif (is_string($object)) {
            static::$classes[$className] = new $object();
        } elseif (is_object($object)) {
            static::$classes[$className] = $object;
        }

    }


    public static function isInstance($object,$className)
    {
        $instance = static::class($className);

        return ($object instanceOf $instance);
    }


    public static function instance()
    {
      return new static;
    }


    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet ( $offset )
    {
        return $this->class($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet ( $offset , $value )
    {
        $this->register($offset,$value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset ( $offset )
    {
      if(isset(static::$classes[$offset])) {
        unset(static::$classes[$offset]);
      }
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists ( $offset )
    {
        return isset(static::$classes[$offset]);
    }


}
