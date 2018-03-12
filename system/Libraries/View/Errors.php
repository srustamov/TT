<?php

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category   View/Errors
 */

namespace System\Libraries\View;


class Errors implements \ArrayAccess , \Countable
{


    protected $errors = [];


    public function __construct (array $errors = [])
    {
        $this->errors = array_merge($this->errors,$errors);
    }


    public function first($key)
    {
      if (isset($this->errors[$key])) {
        if (is_array($this->errors[$key])) {
          return $this->errors[$key][0] ?? false;
        } else {
          return $this->errors[$key] ?? false;
        }
      }
      return false;
    }


    public function all()
    {
        return $this->errors;
    }

    public function has($key)
    {
        return isset($this->errors[$key]);
    }


    public function __get($key)
    {
        return $this->errors[$key] ?? false;
    }

    public  function __call ( $name , $arguments )
    {
        return $this->errors[$name];
    }

    public static function __callStatic ( $name , $arguments )
    {
        return (new static)->errors[$name];
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
        return isset($this->errors[$offset]);
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
        return $this->errors[$offset];
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
        $this->errors[$offset] = $value;
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
        if (isset($this->errors[$offset])) {
            unset($this->errors[$offset]);
        }
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count ()
    {
        return !empty($this->errors) ? count($this->errors) : 0;
    }
}
