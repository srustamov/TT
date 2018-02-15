<?php namespace System\Libraries;



/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage  Libraries
 * @category   Url
 */




class Url
{




  public function request()
  {

    $request_uri = urldecode (
            parse_url ( rtrim ( @$_SERVER[ 'REQUEST_URI' ] , '/' ) , PHP_URL_PATH )
        );
    $request_uri = $request_uri == '' ? '/' : $request_uri;

    return $request_uri;
  }



  public function protocol()
  {
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']  != 'off')
    {
      return 'https';
    }
    return 'http';
  }



  public function base ( $url = ''):String
  {
    $base_url = trim(config('config.base_url'));
    if(empty($base_url))
    {
      $base_url = $this->protocol()."://".$_SERVER['HTTP_HOST'].rtrim(
        str_replace(
          basename(
            $_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']
          ),
          '/'
        );
    }
    return $base_url . '/' . ltrim($url,'/');
  }




  function current ( $url = null ):String
  {
      return rtrim($this->base(). $_SERVER['REQUEST_URI'],'/') . '/' . $url;
  }



  public function segment ( Int $number )
  {
      $url = array_filter ( explode ( '/' , $this->request() ) );
      return $url[ $number ] ?? false;
  }



  public function segments ():Array
  {
      return  array_filter ( explode ( '/' , $this->request() ) );
  }




}
