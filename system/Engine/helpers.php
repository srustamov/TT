<?php
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 * @category Helper functions
 */



use System\Facades\Load;


function app(String $class,Array $args = [])
{
    return Load::class($class,$args);
}


function config(String $name, $default = null)
{
    return Load::config($name, $default);
}


function setting($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}


function import(String $file)
{
    $file = str_replace(['/','\\'], DS, trim($file));

    if (file_exists($file))
    {
        return require_once $file;
    }
    else
    {
        throw new Exception("File not found. Path: [ {$file} ]");
    }
}


function import_dir_files($dir,$once = false)
{
    if ($once)
    {
        foreach (glob($dir."/*") as $file)
        {
            require_once $file;
        }
    }
    else
    {
        foreach (glob($dir."/*") as $file)
        {
            require $file;
        }
    }
}


function storage_dir($path = '')
{
    return path($path, 'storage');
}



function app_dir($path = '')
{
    return path($path, 'app');
}



function system_dir($path = '')
{
    return path($path, 'system');
}



function public_dir($path = '')
{
    return PUBLIC_DIR.'/'.ltrim($path, '/');
}



function path( $path, $path_name = null)
{
    return BASEPATH.DS.(is_null($path_name) ? '' : ltrim($path_name.DS, '/')).ltrim($path, '/');
}



function abort(Int $http_code)
{
    if (file_exists(app_dir('Views/errors/'.$http_code.'.blade.php')))
    {
        $content =  view('errors.'.$http_code);
    }

    $response = Load::class('response')->make($content ?? null,$http_code);

    $response->send();

    exit();
}



function InConsole()
{
    return (php_sapi_name() == 'cli' || php_sapi_name() == 'phpdbg');
}



function csrf_token():String
{
    static $token;

    if (is_null($token))
    {
        $token = Load::class('session')->get('_token');
    }

    return $token;
}



function csrf_field():String
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '" />';
}


function route($name,Array $parameters = [])
{
  return Load::class('route')->getName($name,$parameters);
}



if (!function_exists ( 'flash' ))
{
    function flash ( $key )
    {
        return Load::class('session')->flash($key);
    }
}


if (!function_exists ( 'is_base64' ))
{
    function is_base64 ( String $string ):Bool
    {
        return base64_encode ( base64_decode ( $string ) ) == $string;
    }
}



if (!function_exists ( 'response' ))
{
    function response ()
    {
        return Load::class('response',func_get_args());
    }
}


if (!function_exists ( 'json' ))
{
    function json ( $data )
    {
      return Load::class('response')->json($data);
    }
}


if (!function_exists ( 'report' ))
{
    function report ( String $subject , String $message , $destination = null )
    {
        if (empty( $destination ))
        {
            $destination = str_replace ( ' ' , '-' , $subject );
        }

        $logDir = path ( 'storage/logs/' );

        $extension = '.report';

        if (!is_dir ( $logDir ))
        {
            mkdir ( $logDir , 0755 , true );
        }

        $report = '----------------------------' . PHP_EOL .
                  ' Report                     ' . PHP_EOL .
                  '----------------------------' . PHP_EOL .
                  '|IP: ' . Load::class('http')->ip () . PHP_EOL .
                  '|Subject: ' . $subject . PHP_EOL .
                  '|File: ' . debug_backtrace ()[ 0 ][ 'file' ] ?? '' . PHP_EOL .
                  '|Line: ' . debug_backtrace ()[ 0 ][ 'line' ] ?? '' . PHP_EOL .
                  '|Date: ' . strftime ( '%d %B %Y %H:%M:%S' ) . PHP_EOL .
                  '|Message: ' . $message . PHP_EOL . PHP_EOL . PHP_EOL;
        return Load::class('file')->append ( $logDir . $destination . $extension , $report );
    }
}


if (!function_exists ( 'env' ))
{
    function env ( $name )
    {
        if (function_exists ( 'getenv' ))
        {
            if (getenv ( $name ))
            {
                return getenv ( $name );
            }
        }
        if (function_exists ( 'apache_getenv' ))
        {
            if (apache_getenv ( $name ))
            {
                return apache_getenv ( $name );
            }
        }

        return $_ENV[ $name ] ?? $_SERVER[ $name ] ?? false;
    }
}


if (!function_exists ( 'cookie' ))
{
    function cookie()
    {

        if(func_num_args() == 0)
        {
            return Load::class('cookie');
        }
        else if(func_num_args() == 1)
        {
            return Load::class('cookie')->get(func_get_args()[0]);
        }
        else
        {
            return Load::class('cookie',func_get_args());
        }

    }
}


if (!function_exists ( 'cache' ))
{
    function cache ()
    {
        if (func_num_args() == 0)
        {
            return Load::class('cache');
        }
        elseif (func_num_args() == 1)
        {
            return Load::class('cache')->get ( func_get_arg(0) );
        }
        else
        {
            return Load::class('cache')->put (...func_get_args());
        }
    }
}


if (!function_exists ( 'session' ))
{
    function session ()
    {
        if (func_num_args() == 0)
        {
            return Load::class('session');
        }
        elseif (func_num_args() == 1)
        {
            return Load::class('session')->get (func_get_arg(0));
        }
        else
        {
            return Load::class('session')->set (...func_get_args());
        }
    }
}


if (!function_exists ( 'view' ))
{
    function view ( String $file , $data = [] , $cache = false )
    {
        return Load::class('view')->render ( $file , $data , $cache );
    }
}


if (!function_exists ( 'redirect' ))
{
    function redirect ( $link = false , $refresh = 0 , $http_response_code = 302 )
    {
        if ($link)
        {
            return Load::class('redirect')->to( $link , $refresh , $http_response_code );
        }
        else
        {
            return Load::class('redirect');
        }
    }
}



if (!function_exists ( 'lang' ))
{
    function lang ( $word = null , $replace = [] )
    {
        if (!is_null ( $word ))
        {
            return Load::class('language')->translate ( $word , $replace );
        }
        else
        {
            return Load::class('language');
        }
    }
}


if (!function_exists ( 'validator' ))
{
    function validator ( $data = null , $rules = [] )
    {
        if (!is_null ( $data ))
        {
            return Load::class('validator')->make ( $data , $rules );
        }
        else
        {
            return Load::class('validator');
        }
    }
}


if (!function_exists ( 'get' ))
{
    function get ( $name = false )
    {
        return Load::class('input')->get ( $name );
    }
}


if (!function_exists ( 'post' ))
{

    function post ( $name = false)
    {
        return Load::class('input')->post ( $name  );
    }
}


if (!function_exists ( 'request' ))
{
    function request ()
    {
        if (func_num_args() == 0)
        {
            return Load::class('request');
        }
        elseif (func_num_args() == 1)
        {
            return Load::class('request')->{func_get_arg(0)};
        }
        else
        {
            return Load::class('request')->{func_get_arg(0)} = func_get_arg(1);
        }
    }
}


if (!function_exists ( 'xssClean' ))
{
    function xssClean ( $data )
    {
        return Load::class('input')->xssClean ( $data );
    }
}


if (!function_exists ( 'fullTrim' ))
{
    function fullTrim ( $str , $char = ' ' ): String
    {
        return str_replace ( $char , '' , $str );
    }
}


if (!function_exists ( 'encode_php_tag' ))
{
    function encode_php_tag ( $str ): String
    {
        return str_replace ( array( '<?' , '?>' ) , array( '&lt;?' , '?&gt;' ) , $str );
    }
}


if (!function_exists ( 'preg_replace_array' ))
{

    function preg_replace_array ( $pattern , array $replacements , $subject ): String
    {
        $callback = function () use ( &$replacements ) {
            foreach ($replacements as $key => $value)
            {
                return array_shift ( $replacements );
            }
        };

        return preg_replace_callback ( $pattern , $callback , $subject );
    }
}


if (!function_exists ( 'str_replace_first' ))
{
    function str_replace_first ( $search , $replace , $subject ): String
    {
        return Load::class('str')->replace_first ( $search , $replace , $subject );
    }
}


if (!function_exists ( 'str_replace_last' ))
{
    function str_replace_last ( $search , $replace , $subject ): String
    {
        return Load::class('str')->replace_last ( $search , $replace , $subject );
    }
}


if (!function_exists ( 'str_slug' ))
{
    function str_slug ( $str , $separator = '-' ): String
    {
        return Load::class('str')->slug ( $str , $separator );
    }
}


if (!function_exists ( 'str_limit' ))
{
    function str_limit ( $str , $limit = 100,$end = '...' ): String
    {
        return Load::class('str')->limit ( $str , $limit , $end );
    }
}


if (!function_exists ( 'upper' ))
{

    function upper ( String $str , $encoding = 'UTF-8' ): String
    {
        return mb_strtoupper ( $str , $encoding );
    }
}


if (!function_exists ( 'lower' ))
{

    function lower ( String $str , $encoding = 'UTF-8' ): String
    {
        return mb_strtolower ( $str , $encoding );
    }
}


if (!function_exists ( 'title' ))
{
    /**
     * @param $str
     * @return string
     */
    function title ( String $str , $encoding = 'UTF-8' ): String
    {
        return mb_convert_case ( $str , MB_CASE_TITLE , $encoding );
    }
}


if (!function_exists ( 'len' ))
{
    /**
     * @param array|string $value
     * @param null|string $encoding
     * @return int|bool
     */
    function len ( $value , $encoding = null )
    {
        if (is_string ( $value ))
        {
            return mb_strlen ( $value , $encoding );
        }
        elseif (is_array ( $value ))
        {
            return count ( $value );
        }
        else
        {
            return 0;
        }

    }
}


if (!function_exists ( 'str_replace_array' ))
{

    function str_replace_array ( $search , array $replace , $subject ): String
    {
        return Load::class('str')->replace_array ( $search , $replace , $subject );
    }
}


if (!function_exists ( 'url' ))
{
    function url ($url = null, $parameters = [])
    {
        if (is_null($url))
        {
            return Load::class('url');
        }
        else
        {
            return Load::class('url')->to (...func_get_args());
        }

    }
}


if (!function_exists ( 'current_url' ))
{
    function current_url ( $url = '' ): String
    {
        return Load::class('url')->current ( $url );
    }
}


if (!function_exists ( 'clean_url' ))
{
    function clean_url ( $url ): String
    {
        if ($url == '') return '';

        $url = str_replace ( "http://" , "" , strtolower ( $url ) );

        $url = str_replace ( "https://" , "" , $url );

        if (substr ( $url , 0 , 4 ) == 'www.')
        {
            $url = substr ( $url , 4 );
        }
        $url = explode ( '/' , $url );

        $url = reset ( $url );

        $url = explode ( ':' , $url );

        $url = reset ( $url );

        return $url;
    }
}


if (!function_exists ( 'segment' ))
{
    function segment ( Int $number )
    {
        return Load::class('url')->segment ( $number );
    }
}


if (!function_exists ( 'debug' ))
{
    function debug ( $data )
    {
        ob_get_clean ();

        if (is_array ( $data ))
        {
            echo '<pre style="background-color:#fff; color:#222; line-height:1.2em; font-weight:normal; font:12px Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:100000">';
            print_r($data);
            echo "</pre>";
        }
        else
        {
            var_dump ( $data );
        }
        die(1);
    }
}



if (!function_exists ( 'is_mail' ))
{
    function is_mail ( String $mail )
    {
        return Load::class('validator')->is_mail ( $mail );
    }
}


if (!function_exists ( 'is_url' ))
{
    function is_url ( String $url )
    {
        return Load::class('validator')->is_url ( $url );
    }
}


if (!function_exists ( 'is_ip' ))
{
    function is_ip ( $ip )
    {
        return Load::class('validator')->is_ip ( $ip );
    }
}


if (!function_exists ( 'css' ))
{
    function css ( $file , $modifiedTime = false ): String
    {
        return Load::class('html')->css ( $file , $modifiedTime );
    }
}


if (!function_exists ( 'js' ))
{
    function js ( $file , $modifiedTime = false ): String
    {
        return Load::class('html')->js ( $file , $modifiedTime );
    }
}


if (!function_exists ( 'img' ))
{
    function img ( $file , $attributes = [] ): String
    {
        return Load::class('html')->img ( $file , $attributes );
    }
}
