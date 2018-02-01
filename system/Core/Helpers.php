<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */


use System\Libraries\Language;
use System\Libraries\Session\Session;
use System\Libraries\Str;
use System\Libraries\View;
use System\Libraries\Url;
use System\Libraries\Input;
use System\Libraries\Validator;
use System\Libraries\Redirect;
use System\Libraries\Cookie;
use System\Libraries\Request;
use System\Facades\File;
use System\Facades\Html;
use System\Facades\Http;







function is_base64(String $string)
{
  return base64_encode(base64_decode($string)) == $string;
}




if (!function_exists('report'))
{
    function report(String $subject, String $message,  $destination = null)
    {
        if (empty($destination)) {
            $destination = str_replace(' ', '-', $subject);
        }

        $logDir = BASEDIR . '/storage/logs/';
        $extension = '.report';

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

      $report  = '----------------------------' . PHP_EOL .
                 ' Report                     ' . PHP_EOL .
                 '----------------------------' . PHP_EOL .
                 '|IP: ' . Http::ip() . PHP_EOL .
                 '|Subject: ' . $subject . PHP_EOL .
                 '|File: ' . @debug_backtrace()[0]['file']. PHP_EOL .
                 '|Line: ' . @debug_backtrace()[0]['line']. PHP_EOL .
                 '|Date: ' . strftime('%d %B %Y %H:%M:%S') . PHP_EOL .
                 '|Message: ' . $message . PHP_EOL. PHP_EOL. PHP_EOL;
       return File::append($logDir . $destination . $extension,$report);
    }
}






if (!function_exists('cookie'))
{
    function cookie($key = false, $value = false, $time = null, $path = '/', $domain = '', $secure = false, $http_only = true)
    {
        if ($key && !$value)
        {
            return (new Cookie())->get($key);
        }
        elseif (!$key)
        {
          return (new Cookie());
        }
        else
        {
          return (new Cookie())->http_only($http_only)
                 ->path($path)
                 ->domain($domain)
                 ->secure($secure)
                 ->set($key, $value, $time);
        }
    }
}



if (!function_exists('cache'))
{
    function cache($key = false, $value = false ,$expires = 10)
    {
        if(!$key)
        {
          return (new System\Libraries\Cache());
        }
        elseif (!$value)
        {
          return (new System\Libraries\Cache())->get($key);
        }
        elseif ($key && $value)
        {
          return (new System\Libraries\Cache())->put($key,$value,$expires);
        }
    }
}



if (!function_exists('session')) {
    function session($key = null,$value = false)
    {
        if (is_null($key))
        {
            return (new Session());
        }
        elseif ($key && !$value)
        {
            return (new Session())->get($key);
        }
        else
        {
            return (new Session())->set($key,$value);
        }
    }
}



if (!function_exists('view'))
{
    function view(String $file, $data = [], $cache = true)
    {
        return (new View())->render($file, $data, $cache);
    }
}



if (!function_exists('csrf_token'))
{
    function csrf_token():String
    {
        static $token;

        if (is_null($token))
        {
            if(!app('session')->has('csrf_token_data'))
            {
               app('session')->set('csrf_token_data', base64_encode(openssl_random_pseudo_bytes(32)));
            }
            $token = app('session')->get('csrf_token_data');
        }
        return $token;
    }
}


if (!function_exists('csrf_check'))
{
    function csrf_check():Bool
    {
        $token = app('session')->get('csrf_token_data');
        $user_token = $_POST['_token'] ?? null;
        return ($token === $user_token);
    }
}


if (!function_exists('csrf_field'))
{
    function csrf_field():String
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '" />';
    }
}


if (!function_exists('redirect'))
{
    function redirect($link = false, $refresh = 0, $http_response_code = 302)
    {
        if ($link)
        {
            return new Redirect($link, $refresh, $http_response_code);
        }
        else
        {
            return (new Redirect);
        }
    }
}


if (!function_exists('set_lang'))
{
    function set_lang($lang = null)
    {
        return (new Language())->set($lang);
    }
}


if (!function_exists('lang'))
{
    function lang($word = null, $replace = [])
    {
        if(is_null($word))
        {
          return (new Language());
        }
        else
        {
          return (new Language())->translate($word, $replace);
        }
    }
}





if (!function_exists('validator'))
{
    function validator($data = null, $rules = [])
    {
        if (!is_null($data))
        {
            return (new Validator())->make($data, $rules);
        }
        else
        {
            return (new Validator());
        }
    }
}





if (!function_exists('get'))
{
    /**
     * @param $name
     * @param bool $xss_clean
     * @return array|bool|string
     */
    function get($name = null, $xss_clean = false)
    {
        return (new Input())->get($name, $xss_clean);
    }
}




if (!function_exists('post'))
{
    /**
     * @param $name
     * @param bool $xss_clean
     * @return array|bool|string
     */
    function post($name = null, $xss_clean = false)
    {
        return (new Input())->post($name, $xss_clean);
    }
}




if (!function_exists('request'))
{
    function request($name = null)
    {
        return !is_null($name) ? (new Request())->{$name} : (new Request());
    }
}




if (!function_exists('xssClean'))
{
    /**
     * @param $data
     * @return mixed|string
     */
    function xssClean($data, $image = false)
    {
        return (new Input())->xssClean($data, $image);
    }
}


if (!function_exists('fullTrim')) {
    /**
     * @param $str
     * @param string $char
     * @return String
     */
    function fullTrim($str, $char = ' '):String
    {
        return str_replace($char, '', $str);
    }
}


if (!function_exists('encode_php_tag'))
{
    /**
     * @param $str
     * @return String
     */
    function encode_php_tag($str):String
    {
        return str_replace(array( '<?' , '?>' ), array( '&lt;?' , '?&gt;' ), $str);
    }
}






if (!function_exists('preg_replace_array')) {
    /**
     * @param  string $pattern
     * @param  array $replacements
     * @param  string $subject
     * @return string
     */
    function preg_replace_array($pattern, array $replacements, $subject):String
    {
        $callback = function () use (&$replacements)
        {
          foreach ($replacements as $key => $value)
          {
            return array_shift($replacements);
          }
        };

        return preg_replace_callback($pattern,$callback, $subject);


    }
}




if (!function_exists('str_replace_first'))
{
    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @return String
     */
    function str_replace_first($search, $replace, $subject):String
    {
        return (new Str())->replace_first($search, $replace, $subject);
    }
}


if (!function_exists('str_replace_last'))
{
    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @return mixed
     */
    function str_replace_last($search, $replace, $subject):String
    {
        return (new Str())->replace_last($search, $replace, $subject);
    }
}


if (!function_exists('str_slug'))
{
    /**
     * @param $str
     * @param $separator
     * @return string
     */
    function str_slug($str, $separator = '-'): String
    {
        return (new Str())->slug($str, $separator);
    }
}


if (!function_exists('upper'))
{
    /**
     * @param $str
     * @return string
     */
    function upper(String $str,$encoding = 'UTF-8'): String
    {
        return mb_strtoupper($str, $encoding);
    }
}


if (!function_exists('lower'))
{
    /**
     * @param $str
     * @return string
     */
    function lower(String $str,$encoding = 'UTF-8'): String
    {
        return mb_strtolower($str, $encoding);
    }
}


if (!function_exists('title')) {
    /**
     * @param $str
     * @return string
     */
    function title(String $str,$encoding = 'UTF-8'): String
    {
        return mb_convert_case($str,MB_CASE_TITLE,$encoding);
    }
}


if (!function_exists('len'))
{
    /**
     * @param $value
     * @param null $encoding
     * @return int
     */
    function len(String $value, $encoding = null):Int
    {
        return mb_strlen($value, $encoding);
    }
}


if (!function_exists('str_replace_array')) {

    /**
     * @param  string $search
     * @param  array $replace
     * @param  string $subject
     * @return string
     */

    function str_replace_array($search, array $replace, $subject): String
    {
        return (new Str())->replace_array($search, $replace, $subject);
    }
}


if (!function_exists('url'))
{
    /**
     * @param null $url
     * @return string
     */
    function url($url = null)
    {
        if (is_null($url))
        {
          return (new Url());
        }
        else
        {
          return (new Url())->base($url);
        }

    }
}


if (!function_exists('current_url')) {
    /**
     * @param null $url
     * @return string
     */
    function current_url($url = ''):String
    {
        return (new Url())->current($url);
    }
}


if (!function_exists('clean_url')) {
    /**
     * @param $url
     * @return string
     */
    function clean_url($url):String
    {
        if ($url == '') return '';


        $url = str_replace("http://", "", strtolower($url));

        $url = str_replace("https://", "", $url);

        if (substr($url, 0, 4) == 'www.')
        {
            $url = substr($url, 4);
        }
        $url = explode('/', $url);

        $url = reset($url);

        $url = explode(':', $url);

        $url = reset($url);

        return $url;
    }
}


if (!function_exists('segment')) {

  /**
   * @param Int $number
   * @return string|bool
   */
  function segment(Int $number)
  {
      return (new Url())->segment($number);
  }
}



if (!function_exists('debug'))
{
    function debug($data)
    {
        ob_get_clean();

        if(is_array($data))
        {
          echo '<pre style="font-size:14px;word-wrap:break-word; white-space: pre-wrap;color:rgb(54, 12, 51)">';
          print_r($data);
          echo "</pre>";
        }
        else
        {
          var_dump($data);
        }
        die();
    }
}


if (!function_exists('valid_mail'))
{
    function valid_mail(String $mail)
    {
        return (new Validator())->is_mail($mail);
    }
}




if (!function_exists('valid_url'))
{
    function valid_url(String $url)
    {
        return (new Validator())->is_url($url);
    }
}


if (!function_exists('valid_ip'))
{
    function valid_ip($ip)
    {
        return (new Validator())->is_ip($ip);
    }
}


if (!function_exists('get_css'))
{
    function get_css($file,$modifiedTime = false):String
    {
        return Html::css($file,$modifiedTime);
    }
}


if (!function_exists('get_js'))
{
    function get_js($file,$modifiedTime = false):String
    {
        return Html::js($file,$modifiedTime);
    }
}


if (!function_exists('get_img'))
{
    function get_img($file,$attributes = []):String
    {
        return Html::img($file,$attributes);
    }
}


if (!function_exists('get_bootstrap_css'))
{
    function get_bootstrap_css():String
    {
        return Html::BootstrapCss();
    }
}


if (!function_exists('get_bootstrap_js'))
{
    function get_bootstrap_js():String
    {
        return Html::BootstrapJs();
    }
}


if (!function_exists('get_font_awesome'))
{
    function get_font_awesome():String
    {
        return Html::FontAwesome();
    }
}


if (!function_exists('get_jquery'))
{
    function get_jquery():String
    {
        return Html::jquery();
    }
}


if (!function_exists('get_jqueryUi'))
{
    function get_jqueryUi():String
    {
        return Html::JqueryUi();
    }
}


if (!function_exists('get_angular'))
{
    function get_angular():String
    {
        return Html::angular();
    }
}
