<?php namespace System\Libraries;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category   Url
 */

use System\Engine\Load;
use System\Facades\Route;

class Url
{


    public function to($url = '',array $parameters = [])
    {
        return $this->scheme().'://'.$this->host().'/'.
            (
            !empty($parameters)
                ? trim($url,'/').'/?'.http_build_query($parameters)
                : ltrim($url,'/')
            );
    }


    public function route($name,Array $parameters = [])
    {
      return Route::getName($name,$parameters);
    }



    /**
     * @return string
     */
    public function request()
    {
        $request = urldecode (
            parse_url ($_SERVER[ 'REQUEST_URI' ] ?? '/' , PHP_URL_PATH )
        );
        $request = str_replace ( ' ' , '' , $request );

        return  ($request == '' || $request == '/') ? '/' : rtrim($request,'/');
    }


    /**
     * @return String
     */
    public function scheme()
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
     * @param array $parameters
     * @return String
     */
    public function base ( $url = '',array $parameters = []):String
    {
        if(preg_match('/^(https?:\/\/)/',$url))
        {
            return trim($url,'/').(
                !empty($parameters) ? '/?'.http_build_query($parameters) : '/'
                );
        }
        else
        {
            $base_url = Load::class('config')->get('app.url');

            if(!$base_url || empty($base_url))
            {
                $base_url  = $this->scheme().'://'.$this->host();
            }
            else
            {
                if(!preg_match('/^(https?:\/\/)/',$base_url))
                {
                    $base_url = $this->scheme().'://'.$base_url;
                }
            }
        }

        return rtrim($base_url,'/') . '/' . ltrim($url,'/').(
            !empty($parameters) ? '/?'.http_build_query($parameters) : ''
            );
    }


    /**
     * @param null $url
     * @return String
     */
    public function current ( $url = null ):String
    {
        return $this->scheme().'://'.$this->host().'/'.trim ( $this->request() , '/' ) . '/' . $url;
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
        return array_filter ( explode ( '/' , $this->request() ) );
    }




}
