<?php namespace System\Libraries;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */

use System\Libraries\Session\Session;
use System\Libraries\Cookie;
use System\Libraries\Input;


class Request
{


  public function __get($key)
  {
    return $_REQUEST[$key] ?? false;
  }


  public function __set($key,$value)
  {
    $_REQUEST[$key] = $value;
  }




  public function all()
  {
    if(isset($_REQUEST['_token']))
    {
      unset($_REQUEST['_token']);
    }
    return $_REQUEST;
  }


  public function session($key,$encode = true)
  {
    return app('session')->get($key,$encode);
  }


  public function cookie($key)
  {
    return app('cookie')->get($key);
  }


  public function server($key)
  {
    return $_SERVER[strtoupper($key)] ?? false;
  }


  public function input($name)
  {
    if(isset($_REQUEST[$name]))
    {
        return app('input')->xssClean($_REQUEST[$name]);
    }
    return false;
  }


  public function file($name)
  {
    if(isset($_FILES[$name]))
    {
      if($_FILES[$name]['error'] > 0)
      {
        return false;
      }
      else
      {
        return $_FILES[$name];
      }
    }
    else
    {
      return false;
    }
  }


  public function post($name)
  {
    return app('input')->post($name);
  }


  public function get($name)
  {
    return app('input')->get($name);
  }


  public function method():String
  {
    return $this->server('request_method');
  }


  public function ajax():String
  {
    return app('http')->isAjax();
  }


  public function controller()
  {
    return $this->server('called_controller');
  }


  public function action()
  {
    return $this->server('called_method');
  }




}
