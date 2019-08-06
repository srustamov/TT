<?php namespace System\Libraries\Encrypt;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library/Encrypt
 * @category    OpenSSL
 */

use Exception;
use System\Exceptions\EncryptException;
use System\Facades\Config;

class OpenSsl
{
    private $method = 'AES-256-CBC';


    private $key;


    private $iv;


    private $option = OPENSSL_RAW_DATA;


    /**
     * OpenSsl constructor.
     * @throws EncryptException
     * @throws Exception
     */
    public function __construct()
    {
        $key = Config::get('app.key', false);

        if (!$key && !CONSOLE) {
            throw new EncryptException('Application Down! Application Encryption key not found!');
        }

        $this->key = $key;
    }


    /**
     * @param $method
     * @return $this
     */
    public function method($method): self
    {
        if (in_array(strtoupper($method), openssl_get_cipher_methods(), true)) {
            $this->method = $method;
        }
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function key($key): self
    {
        $this->key = $key;
        return $this;
    }


    /**
     * @param $iv
     * @return $this
     */
    public function iv($iv): self
    {
        $this->iv = $iv;
        return $this;
    }


    /**
     * @param $option
     * @return $this
     */
    public function option($option): self
    {
        $this->option = $option;
        return $this;
    }


    /**
     * @param $data
     * @return String
     */
    public function encrypt($data):String
    {
        $encrypted_data = openssl_encrypt($data, $this->method, $this->key, $this->option, $this->getIv());
        return base64_encode($encrypted_data);
    }


    /**
     * @param $data
     * @return String
     */
    public function decrypt($data):String
    {
        $decoded  = openssl_decrypt(base64_decode($data), $this->method, $this->key, $this->option, $this->getIv());
        return trim($decoded);
    }


    /**
     * @return bool|string
     */
    private function getIv()
    {
        if (is_null($this->iv)) {
            return substr(md5($this->key), 0, 16);
        } else {
            return substr(md5($this->iv), 0, 16);
        }
    }


    /**
     * @param Int $length
     * @return string
     */
    public function random(Int $length): string
    {
        return openssl_random_pseudo_bytes($length);
    }
}
