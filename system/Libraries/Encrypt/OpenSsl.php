<?php namespace System\Libraries\Encrypt;


/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Libraries/Encrypt
 * @category    OpenSSL
 */


class OpenSsl
{
  /**
   * @param $data
   * @param array $parameters
   * @return string
   */
  public function encode ( $data , Array $parameters = [] ):String
  {
      $method = $parameters[ 'method' ] ?? 'AES-128-CBC';
      $key    = $parameters[ 'key' ] ?? config ( 'config.encryption_key' );
      $key    = substr(sha1(md5($key)),0,16);
      $option = $parameters[ 'option' ] ?? OPENSSL_RAW_DATA;
      $iv     = $parameters[ 'iv' ] ?? $key;
      $encrypted_data = openssl_encrypt($data, $method, $key,$option,$iv);
      return base64_encode ( $encrypted_data );

  }


  /**
   * @param $data
   * @param array $parameters
   * @return string
   */
  public function decode ( $data , Array $parameters = [] ):String
  {
      $method = $parameters[ 'method' ] ?? 'AES-128-CBC';
      $key    = $parameters[ 'key' ] ?? config ('config.encryption_key');
      $key    = substr(sha1(md5($key)),0,16);
      $option = $parameters[ 'option' ] ?? OPENSSL_RAW_DATA;
      $iv     = $parameters[ 'iv' ] ?? $key;
      $encrypted_data = base64_decode ( $data );
      $decoded        = openssl_decrypt ( $encrypted_data, $method, $key,$option,$iv );
      return trim ( $decoded );
  }



  public function random(Int $length = 32)
  {
    return openssl_random_pseudo_bytes($length);
  }





}
