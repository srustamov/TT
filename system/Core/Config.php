<?php
/**
 * Created by PhpStorm.
 * User: tt
 * Date: 10/03/18
 * Time: 16:01
 */

namespace System\Core;


class Config
{


    protected $configs = [];




    public function setConfigsItems(Array $items = [])
    {
        $this->configs = $items;
    }


    public function get()
    {

    }

    public function set($key , $value)
    {
        $this->configs[$key] = $value;
    }


    public function push($key,$value)
    {
        $this->configs[$key][] = $value;
    }


    public function remove($key,$item = null)
    {
        if (!is_null($item)) {
            unset($this->configs[$key][$item]);
        } else {
            unset($this->configs[$key]);
        }

    }
}