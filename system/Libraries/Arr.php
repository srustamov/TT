<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Library
 * @category    Arrays
 */



class Arr
{


    /**
     * @param array $array
     * @return bool
     */
    public function isAssoc(array $array):Bool
    {
        $keys = array_keys($array);
        return (array_keys($keys) !== $keys);
    }


    public function each(&$array,\Closure $callback)
    {
      foreach ($array as $key => $value)
      {
        $callback($key,$value);
      }

    }

    /**
     * @param array $array
     * @param $key
     * @return bool|mixed
     */
    public function get(array $array, $key)
    {
        return $array[ $key ] ?? false;
    }


    /**
     * @param array $array
     * @param $key
     * @return array
     */
    public function forget(array &$array, $key):array
    {
        if($this->exists($array, $key)) {
            unset($array[ $key ]);
        }
        return $array;
    }

    /**
     * @param array $array
     * @param $key
     * @return bool
     */
    public function exists(array $array, $key):Bool
    {
        return array_key_exists($key, $array);
    }



}
