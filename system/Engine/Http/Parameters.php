<?php namespace System\Engine\Http;

use Countable;
use ArrayAccess;
use System\Libraries\Arr;

class Parameters implements ArrayAccess, Countable
{
    private $parameters;


    public function __construct(array $parameters = [])
    {
        $this->make($parameters);
    }

    public function make(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }


    public function get($key, $default = false)
    {
        return $this->parameters[$key] ?? $default;
    }

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $name => $_value) {
                $this->parameters[$name] = $_value;
            }
        } else {
            $this->parameters[$key] = $value;
        }
    }


    public function add($key, $value = null)
    {
        $this->set($key, $value);
    }

    public function all()
    {
        return $this->parameters;
    }


    public function has($key)
    {
        return isset($this->parameters[$key]);
    }


    public function remove($key)
    {
        if ($this->has($key)) {
            unset($this->parameters[$key]);
        }
    }



    public function only()
    {
        if (func_num_args() === 0) {
            return [];
        }

        $only = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

        return Arr::only($this->all(), $only);
    }


    public function except()
    {
        if (func_num_args() === 0) {
            return $this->all();
        }

        $excepts = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

        return Arr::except($this->all(), $excepts);
    }


    public function map($callback)
    {
        foreach ($this->parameters as $key => $value) {
            $this->parameters[$key] = $callback($value, $key);
        }

        return $this;
    }


    public function filter($callback)
    {
        $this->parameters = array_filter(
            $this->parameters,
            $callback,
            ARRAY_FILTER_USE_BOTH
        );

        return $this;
    }


    public function __get($key)
    {
        return $this->get($key);
    }


    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __set($key, $value)
    {
        return $this->set($key, $value);
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
    public function offsetGet($offset)
    {
        return $this->get($offset);
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
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
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
    public function offsetUnset($offset)
    {
        if ($this->has($offset)) {
            $this->remove($offset);
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
    public function offsetExists($offset)
    {
        return $this->has($offset);
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
    public function count()
    {
        return count($this->parameters);
    }
}
