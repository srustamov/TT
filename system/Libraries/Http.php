<?php namespace System\Libraries;

/**
 * @package TT
 * @author Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage Library
 * @category  Http
 */



class Http
{


    /**
     * @return String
     */
    public function userAgent(): String
    {
        return $_SERVER[ 'HTTP_USER_AGENT' ]  ?? '';
    }


    /**
     * @return String
     */
    public function host(): String
    {
        return $_SERVER[ 'HTTP_HOST' ] ?? '';
    }


    /**
     * @return String
     */
    public function name(): String
    {
        return $_SERVER[ 'SERVER_NAME' ] ?? '';
    }


    /**
     * @return String
     */
    public function language(): String
    {
        return substr($_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ?? 'en', 0, 2);
    }


    /**
     * @return String
     */
    public function encoding(): String
    {
        return $_SERVER[ 'HTTP_ACCEPT_ENCODING' ] ?? '';
    }


    /**
     * @param null $key
     * @return array | bool
     */
    public function cookie($key = null)
    {
        if (isset($_SERVER[ 'HTTP_COOKIE' ])) {
            $cookies_string_array = explode(';', $_SERVER[ 'HTTP_COOKIE' ]);

            $cookies = [];

            foreach ($cookies_string_array as $value) {
                list($cookie_key, $cookie_value) = explode('=', $value, 2);

                $cookies[$cookie_key] = $cookie_value;
            }

            if ($key !== null) {
                return $cookies[$key] ?? false;
            }
            return !empty($cookies) ? $cookies : false;
        }

        return false;
    }


    /**
     * @return String
     */
    public function connection(): String
    {
        return $_SERVER[ 'HTTP_CONNECTION' ] ?? '';
    }


    /**
     * @return bool|String
     */
    public function referer()
    {
        if (!isset($_SERVER[ 'HTTP_REFERER' ]) || trim($_SERVER[ 'HTTP_REFERER' ]) == '') {
            return false;
        }
        return trim($_SERVER[ 'HTTP_REFERER' ]);
    }


    /**
     * @return String
     */
    public function ip(): String
    {
        if (!empty($_SERVER[ 'HTTP_CLIENT_IP' ])) {
            $ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
        } elseif (!empty($_SERVER[ 'HTTP_X_FORWARDED_FOR' ])) {
            $ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
        } else {
            $ip = $_SERVER[ 'REMOTE_ADDR' ] ?? '';
        }

        return $ip;
    }


    /**
     * @return bool
     */
    public function isRobot(): Bool
    {
        return  (isset($_SERVER[ 'HTTP_USER_AGENT' ]) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER[ 'HTTP_USER_AGENT' ])) ;
    }


    /**
     * @return bool
     */
    public function isMobile(): Bool
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER[ "HTTP_USER_AGENT" ]);
    }


    /**
     * @return bool
     */
    public function isAjax(): Bool
    {
        return  (isset($_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) && $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] === 'XMLHttpRequest');
    }
}
