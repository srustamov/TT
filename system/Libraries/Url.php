<?php namespace System\Libraries;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category   Url
 */



class Url
{


    protected $baseUrl;


    /**
     * @param String|null $baseUrl
     */
    public function setBase( String $baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
    }


    /**
     * @return mixed|string
     */
    public function request()
    {

        $request = urldecode (
            parse_url ( rtrim ( @$_SERVER[ 'REQUEST_URI' ] , '/' ) , PHP_URL_PATH )
        );
        $request = str_replace ( ' ' , '' , $request );

        return $request == '' ? '/' : $request;
    }


    /**
     * @return string
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
     * @param string $url
     * @return String
     */
    public function base ( $url = ''):String
    {
        if(!is_null($this->baseUrl)) {
            if(!preg_match('/^(https?://)/',$this->baseUrl)) {
                $base_url = $this->protocol().'://'.$this->baseUrl;
            } else {
                $base_url = $this->baseUrl;
            }

        } else {
            $base_url = trim(config('config.base_url'));

            if(empty($base_url))
            {
                $base_url = $this->protocol()."://".$this->host();
            }
        }

        return $base_url . '/' . ltrim($url,'/');
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
    public  function host()
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
     * @return bool|mixed
     */
    public function segment ( Int $number )
    {
        return $this->segments()[ $number ] ?? false;
    }


    /**
     * @return array
     */
    public function segments ():array
    {
        return  array_filter ( explode ( '/' , $this->request() ) );
    }




}
