<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Arrays
 */



class Arr
{

    /**
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array):Bool
    {
        $keys = array_keys($array);

        return (array_keys($keys) !== $keys);
    }


    /**
     * @param array $array
     * @param \Closure $callback
     * @return \Generator
     */
    public static function each(array &$array, \Closure $callback)
    {
        foreach ($array as $key => $value) {
            yield $callback($key, $value);
        }
    }

    /**
     * @param array $array
     * @param $key
     * @param null $default
     * @return bool|mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (strpos($key, '.')) {
            $item_recursive = explode('.', $key);

            foreach ($item_recursive as $item) {
                if (isset($array[$item])) {
                    $array = $array[$item];
                } else {
                    return $default;
                }
            }

            return $array ?: $default;
        }

        return $array[$key] ?? $default;
    }


    public static function set(&$array, $key, $value)
    {
        if ($key === null) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;

        return $array;
    }


    /**
     * @param array $array
     * @param $key
     * @return mixed
     */
    public static function forget(array &$array, $key)
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            while (count($keys) > 1) {
                $key = array_shift($keys);

                if (! isset($array[$key]) || ! is_array($array[$key])) {
                    return true;
                }

                $array = &$array[$key];
            }

            unset($array[array_shift($keys)]);
        } elseif (array_key_exists($key, $array)) {
            unset($array[$key]);
        }
    }

    /**
     * @param array $array
     * @param $key
     * @return bool
     */
    public static function has(array $array, $key):Bool
    {
        if (strpos($key, '.')) {
            $items_recursive = explode('.', $key);

            foreach ($items_recursive as $item) {
                if (array_key_exists($item, $array)) {
                    $array = $array[$item];
                } else {
                    return false;
                }
            }
            return true;
        }

        return array_key_exists($key, $array);
    }




    public static function only(array $array, array $only): array
    {
        return array_intersect_key($array, array_flip((array) $only));
    }



    public static function except(array $array, array $except): array
    {
        if (static::isAssoc($array)) {
            $array = array_filter($array, static function ($key) use ($except) {
                return !in_array($key, $except, true);
            }, ARRAY_FILTER_USE_KEY);
        } else {
            foreach ($except as $value) {
                if ($position = array_search($value, $array, true)) {
                    unset($array[$position]);
                }
            }
        }

        return $array;
    }
}
