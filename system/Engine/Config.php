<?php namespace System\Engine;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



class Config implements \ArrayAccess, \Countable
{
    private $configurations = [];


    public function __construct(array $configurations = [])
    {
        $this->configurations = $configurations;
    }




    public function has($key)
    {
        if (strpos($key, '.') !== false) {
            $items_recursive = explode('.', $key);

            $config = $this->configurations;

            foreach ($items_recursive as $item) {
                if (array_key_exists($item, $config)) {
                    $config = $config[$item];
                } else {
                    return false;
                }
            }
            return true;
        } else {
            return array_key_exists($key, $this->configurations);
        }
    }


    public function all()
    {
        return $this->configurations;
    }


    public function get($extension, $default = null)
    {
        if (strpos($extension, '.') !== false) {
            $item_recursive = explode('.', $extension);

            $config = $this->configurations;

            foreach ($item_recursive as $item) {
                $config = $config[$item] ?? false;
            }

            return $config ?: $default;
        } else {
            return $this->configurations[$extension] ?? $default;
        }
    }



    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            static::setRecursive($this->configurations, $key, $value);
        }
    }


    public function forget(String $key)
    {
        if (strpos($key, '.') !== false) {
            static::forgetRecursive($this->configurations, $key);
        } else {
            if ($this->has($key)) {
                unset($this->configurations[$key]);
            }
        }
    }


    public function delete($key)
    {
        $this->forget($key);
    }


    public function prepend($key, $value)
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }


    public function push($key, $value)
    {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }


    private static function setRecursive(&$configurations, $key, $value)
    {
        if (is_null($key)) {
            return $configurations = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($configurations[$key]) || ! is_array($configurations[$key])) {
                $configurations[$key] = [];
            }
            $configurations = &$configurations[$key];
        }
        $configurations[array_shift($keys)] = $value;
    }


    private static function forgetRecursive(&$configurations, $key)
    {
        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($configurations[$key]) || ! is_array($configurations[$key])) {
                return true;
            }

            $configurations = &$configurations[$key];
        }

        unset($configurations[array_shift($keys)]);
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
        $this->forget($offset);
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
        return count($this->configurations);
    }
}
