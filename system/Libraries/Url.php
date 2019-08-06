<?php namespace System\Libraries;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category   Url
 */

use System\Engine\App;
use System\Facades\Route;

class Url
{
    public function to($url = '', array $parameters = []): string
    {
        return $this->scheme().'://'.$this->host().'/'.
            (
                !empty($parameters)
                ? trim($url, '/').'/?'.http_build_query($parameters)
                : ltrim($url, '/')
            );
    }


    public function route($name, array $parameters = [])
    {
        return Route::getName($name, $parameters);
    }



    /**
     * @return string
     */
    public function request(): string
    {
        $request = urldecode(
            parse_url($_SERVER[ 'REQUEST_URI' ] ?? '/', PHP_URL_PATH)
        );
        $request = str_replace(' ', '', $request);

        return  ($request === '' || $request === '/') ? '/' : rtrim($request, '/');
    }


    /**
     * @return string
     */
    public function scheme(): string
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']  !== 'off') {
            return 'https';
        }
        return 'http';
    }


    /**
     * @return bool
     */
    public function secure(): bool
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']  !== 'off');
    }


    /**
     * @param String $url
     * @param array $parameters
     * @return String
     */
    public function base($url = '', array $parameters = []):String
    {
        if (preg_match('/^(https?:\/\/)/', $url)) {
            return trim($url, '/').(
                !empty($parameters) ? '/?'.http_build_query($parameters) : '/'
                );
        }

        $base_url = App::get('config')->get('app.url');

        if (!$base_url || empty($base_url)) {
            $base_url  = $this->scheme().'://'.$this->host();
        } else if (!preg_match('/^(https?:\/\/)/', $base_url)) {
            $base_url = $this->scheme().'://'.$base_url;
        }


        return rtrim($base_url, '/') . '/' . ltrim($url, '/').(
            !empty($parameters) ? '/?'.http_build_query($parameters) : ''
            );
    }


    /**
     * @param null $url
     * @return String
     */
    public function current($url = null):String
    {
        return $this->scheme().'://'.$this->host().'/'.trim($this->request(), '/') . '/' . $url;
    }


    /**
     * @return string
     */
    public function host(): string
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host     = $_SERVER['HTTP_X_FORWARDED_HOST'];
            $elements = explode(',', $host);
            $host     = trim(end($elements));
        } else {
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
    public function segment(Int $number)
    {
        return $this->segments()[ $number ] ?? false;
    }


    /**
     * @return array
     */
    public function segments(): array
    {
        return array_filter(explode('/', $this->request()));
    }
}
