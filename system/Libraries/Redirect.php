<?php namespace System\Libraries;




/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage  Library
 * @category   Redirect
 */





class Redirect
{


  protected $link;

  protected $refresh;

  protected $http_response_code;

  protected $back;

  protected $with;

  protected $view_variable;




  public function __construct($link = false,$refresh = 0,$http_response_code = 302)
  {
    $this->link = $link;
    $this->refresh = $refresh;
    $this->http_response_code = $http_response_code;
  }




  public function back ( $refresh = 0 )
  {
    $this->refresh = $refresh;
    if($back = app('http')->referer()) {
      $this->back = $back;
    } else {
      $this->back = 'javascript://history.go(-1)';
    }
    return $this;

  }





  public function to ( String $link, $refresh = 0,$http_response_code = 302 )
  {
    if ( (int) $refresh > 0 ) {
        sleep ( $refresh );
    }

    header ( "Location:" . $link , true,$http_response_code); exit;

  }



  public function __call($method,$args)
  {
    $method = mb_strtolower($method);
    if(mb_strlen($method) >= 4 && mb_substr($method,0,4) == 'with')
    {
      if($method == 'with')
      {
        $this->view_variable = 'errors';
      }
      else
      {
        $this->view_variable = mb_substr($method,4);
      }
      $this->with = (object) $args[0];
    }
    return $this;
  }







  function __destruct ()
  {

    if( $this->with ) {
      app('session')->setArray ([
         md5('redirectWithData') => $this->with ,
        md5('redirectWithVariableName') => mb_strtolower($this->view_variable)
       ]);
    }

    if( $this->link ) {
      if(!preg_match('/https?:\/\//',$this->link)) {
          $this->link = app('url')->base($this->link);
      }
      return $this->to ($this->link,$this->refresh,$this->http_response_code);
    }

    if( $this->back ) {
      return $this->to ($this->back,$this->refresh,$this->http_response_code);
    }

  }

}
