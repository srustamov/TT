<?php namespace System\Libraries;



/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage  Libraries
 * @category   Url
 */


use System\Facades\Html as HtmlDom;

class Url
{




  public function request()
  {

    $request = urldecode (
            parse_url ( rtrim ( @$_SERVER[ 'REQUEST_URI' ] , '/' ) , PHP_URL_PATH )
        );
    $request = str_replace ( ' ' , '' , $request );

    return $request == '' ? '/' : $request;
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
      $base_url = $this->protocol()."://".$_SERVER['HTTP_HOST'];
    }

    return $base_url . '/' . ltrim($url,'/');
  }




  function current ( $url = null ):String
  {
      return rtrim($this->base().trim ( $_SERVER[ 'REQUEST_URI' ] , '/' ),'/') . '/' . $url;
  }



  public function segment ( Int $number )
  {
      return $this->segments()[ $number ] ?? false;
  }



  public function segments ():array
  {
      return  array_filter ( explode ( '/' , $this->request() ) );
  }




}
