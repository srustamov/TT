<?php namespace System\Libraries;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category   Url
 */

use System\Facades\Load;

class Url
{


    protected $baseUrl;


    /**
     * @param String $baseUrl
     */
    public function setBase( String $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }


    /**
     * @return string
     */
    public function request()
    {
        $request = urldecode (
            parse_url ($_SERVER[ 'REQUEST_URI' ] , PHP_URL_PATH )
        );
        $request = str_replace ( ' ' , '' , $request );

        return  ($request == '' || $request == '/') ? '/' : rtrim($request,'/');
    }


    /**
     * @return String
     */
    public function protocol()
    {
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']  != 'off')
        {
            return 'https';
        }
        return 'http';
    }


    /**
    * @return Bool
    */
    public function secure():Bool
    {
      return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']  != 'off');
    }


    /**
     * @param String $url
     * @param Array $parameters
     * @return String
     */
    public function base ( $url = '',$parameters = []):String
    {

        if(preg_match('/^(https?:\/\/)/',$url))
        {
          return trim($url,'/').(
            !empty($parameters) ? '/?'.http_build_query($parameters) : '/'
          );
        }
        else
        {
          if(is_null($this->baseUrl))
          {
            $base_url = Load::config('config.base_url');

            if(!$base_url || empty($base_url))
            {
              $base_url  = $this->protocol().'://'.$this->host();
            }
          }
          else
          {
            $base_url = $this->baseUrl;
          }

          if(!preg_match('/^(https?:\/\/)/',$base_url))
          {
            $base_url = $this->protocol().'://'.$base_url;
          }


        }


        return rtrim($base_url,'/') . '/' . ltrim($url,'/').(
          !empty($parameters) ? '/?'.http_build_query($parameters) : ''
        );
    }


    /**
     * @return String
     */
    public function to()
    {
      return $this->base(...func_get_args());
    }

    /**
     * @param null $url
     * @return String
     */
   public function current ( $url = null ):String
    {
        return rtrim($this->base().trim ( $this->request() , '/' ),'/') . '/' . $url;
    }


    /**
     * @return string
     */
    public function host()
    {
        if( isset($_SERVER['HTTP_X_FORWARDED_HOST']) )
        {
            $host     = $_SERVER['HTTP_X_FORWARDED_HOST'];
            $elements = explode(',', $host);
            $host     = trim(end($elements));
        }
        else
        {
            $host = $_SERVER['HTTP_HOST']   ??
                    $_SERVER['SERVER_NAME'] ??
                    $_SERVER['SERVER_ADDR'] ??
                    '';
        }

        return trim($host);
    }


    /**
     * @param Int $number
     * @return Bool|Mixed
     */
    public function segment ( Int $number )
    {
        return $this->segments()[ $number ] ?? false;
    }


    /**
     * @return Array
     */
    public function segments ():array
    {
        return  array_filter ( explode ( '/' , $this->request() ) );
    }




}
