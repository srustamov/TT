<?php namespace System\Libraries\Encrypt;


/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library/Encrypt
 * @category    OpenSSL
 */

use System\Exceptions\EncryptException;
use System\Facades\Load;

class OpenSsl
{


    private $method = 'AES-128-CBC';


    private $key;


    private $iv;


    private $option = OPENSSL_RAW_DATA;


    /**
     * OpenSsl constructor.
     * @throws EncryptException
     */
    function __construct()
    {
        $key = Load::class('config')->get ('app.key',false);

        if(!$key)
        {
            throw new EncryptException("<b>Application Down</b> ! Application Encryption key not found!");
        }

        $this->key = $key;
    }


    /**
     * @param $method
     * @return $this
     */
    public function method( $method)
    {
        if(in_array(strtoupper($method), openssl_get_cipher_methods()))
        {
            $this->method = $method;
        }
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function key( $key)
    {
        $this->key = $key;
        return $this;
    }


    /**
     * @param $iv
     * @return $this
     */
    public function iv( $iv)
    {
        $this->iv = $iv;
        return $this;
    }


    /**
     * @param $option
     * @return $this
     */
    public function option( $option)
    {
        $this->option = $option;
        return $this;
    }


    /**
     * @param $data
     * @return String
     */
    public function encrypt ( $data):String
    {
        $encrypted_data = openssl_encrypt($data, $this->method, $this->key,$this->option,$this->getIv());
        return base64_encode ($encrypted_data);
    }


    /**
     * @param $data
     * @return String
     */
    public function decrypt ( $data):String
    {
        $decoded  = openssl_decrypt ( base64_decode ( $data ), $this->method, $this->key,$this->option,$this->getIv() );
        return trim ( $decoded );
    }


    /**
     * @return bool|string
     */
    private function getIv()
    {
        if(is_null($this->iv))
        {
            return substr(md5($this->key),0,16);
        }
        else
        {
            return substr(md5($this->iv),0,16);
        }

    }


    /**
     * @param Int $length
     * @return string
     */
    public function random( Int $length)
    {
        return openssl_random_pseudo_bytes($length);
    }





}
