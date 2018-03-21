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
    public function isAssoc(array $array):Bool
    {
        $keys = array_keys($array);

        return (array_keys($keys) !== $keys);
    }


    /**
     * @param $array
     * @param \Closure $callback
     * @return array
     */
    public function each( array &$array, \Closure $callback)
    {
      foreach ($array as $key => $value)
      {
        $callback($key,$value);
      }

      return $array;

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
        if($this->exists($array, $key))
        {
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




    public function only(array $array,array $only)
    {

      if($this->isAssoc($array))
      {
        $_only = array();

        foreach ($only as $value)
        {
          if(isset($array[$value]))
          {
            $_only[$value] = $array[$value];
          }
        }

        return $_only;
      }
      else
      {
        return $array;
      }

    }



    public function except(array $array,array $except)
    {
      if($this->isAssoc($array))
      {
        foreach ($except as $value)
        {
          if(isset($array[$value]))
          {
            unset($array[$value]);
          }
        }
      }
      else
      {
        foreach ($except as $value)
        {
          if(($position = array_search($value, $array)))
          {
            unset($array[$position]);
          }
        }
      }

      return $array;
    }



}
