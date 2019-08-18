<?php
/**
 * Application helper Functions
 *
 * @category Helper_Functions
 * @author   Samir Rustamov <rustemovv96@gmail.com>
 * @link     https://github.com/srustamov/TT
 */


use System\Engine\App;

function app(string $class = null)
{
    if ($class === null) {
        return App::getInstance();
    }

    return App::get($class);
}


if (!function_exists('getAllHeaders')) {
    function getAllHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}


/**
 * @param String|null $name
 * @param null $default
 * @return mixed
 * @throws Exception
 */
function config(String $name = null, $default = null)
{
    if ($name === null) {
        return App::get('config');
    }
    return App::get('config')->get($name, $default);
}


function setting($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}


/**
 * @param String $file
 * @param bool $once
 * @return mixed
 * @throws Exception
 */
function import(String $file, $once = true)
{
    return App::get('file')->import($file, $once);
}


/**
 * @param $directory
 * @param bool $once
 * @throws Exception
 */
function importFiles($directory, $once = true)
{
    foreach (glob(rtrim($directory, DIRECTORY_SEPARATOR) . "/*") as $file) {
        import($file, $once);
    }
}


function storage_path($path = '')
{
    return App::getInstance()->storagePath($path);
}


function app_path($path = '')
{
    return App::getInstance()->appPath($path);
}


function public_path($path = '')
{
    return App::getInstance()->publicPath($path);
}


/**
 * @param string $path
 * @return mixed
 */
function path($path = '')
{
    return App::getInstance()->path($path);
}


/**
 * @param null $word
 * @param array $replace
 * @return mixed
 * @throws Exception
 */
function __($word = null, $replace = [])
{
    return lang($word, $replace);
}

/**
 * @param Int $http_code
 * @param null $message
 * @param array $headers
 * @throws Exception
 */
function abort(Int $http_code, $message = null, $headers = [])
{
    if (file_exists(app_path('Views/errors/' . $http_code . '.blade.php'))) {
        $content = view('errors.' . $http_code);
    }

    $response = App::get('response')->setStatusCode($http_code, $message);

    $response->withHeaders($headers);

    $response->setContent($content ?? null);

    $response->send();

    App::end();
}


/**
 * @return bool
 */
function inConsole()
{
    return CONSOLE;
}


/**
 * @return String
 * @throws Exception
 */
function csrf_token(): String
{
    static $token;

    if ($token === null) {
        $token = App::get('session')->get('_token');
    }

    return $token;
}


function csrf_field(): String
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '" />';
}


/**
 * @param string $name
 * @param array $parameters
 * @return mixed
 * @throws Exception
 */
function route(string $name, array $parameters = [])
{
    return App::get('route')->getName($name, $parameters);
}


if (!function_exists('flash')) {
    /**
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    function flash(string $key)
    {
        return App::get('session')->flash($key);
    }
}


if (!function_exists('is_base64')) {
    /**
     * @param string $string
     * @return bool
     */
    function is_base64(string $string): Bool
    {
        return base64_encode(base64_decode($string)) === $string;
    }
}


if (!function_exists('response')) {
    /**
     * @return mixed
     * @throws Exception
     */
    function response()
    {
        return App::get('response', ...func_get_args());
    }
}


if (!function_exists('json')) {
    /**
     * @param $data
     * @return mixed
     * @throws Exception
     */
    function json($data)
    {
        return App::get('response')->json($data);
    }
}


if (!function_exists('report')) {
    /**
     * @param String $subject
     * @param String $message
     * @param null $destination
     * @return mixed
     * @throws Exception
     */
    function report(String $subject, String $message, $destination = null)
    {
        if (empty($destination)) {
            $destination = str_replace(' ', '-', $subject);
        }

        $logDir = path('storage/logs/');

        $extension = '.report';

        if (!is_dir($logDir) && !mkdir($logDir, 0755, true) && !is_dir($logDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $logDir));
        }

        $report = '----------------------------' . PHP_EOL .
            ' Report                     ' . PHP_EOL .
            '----------------------------' . PHP_EOL .
            '|IP: ' . App::get('http')->ip() . PHP_EOL .
            '|Subject: ' . $subject . PHP_EOL .
            '|File: ' . debug_backtrace()[0]['file'] ?? '' . PHP_EOL .
            '|Line: ' . debug_backtrace()[0]['line'] ?? '' . PHP_EOL .
            '|Date: ' . strftime('%d %B %Y %H:%M:%S') . PHP_EOL .
            '|Message: ' . $message . PHP_EOL . PHP_EOL . PHP_EOL;
        return App::get('file')->append($logDir . $destination . $extension, $report);
    }
}


if (!function_exists('env')) {
    /**
     * @param $name
     * @return array|bool|false|mixed|string
     */
    function env($name)
    {
        if (function_exists('getenv') && getenv($name)) {
            return getenv($name);
        }
        if (function_exists('apache_getenv') && apache_getenv($name)) {
            return apache_getenv($name);
        }

        return $_ENV[$name] ?? $_SERVER[$name] ?? false;
    }
}


if (!function_exists('cookie')) {
    /**
     * @return mixed
     * @throws Exception
     */
    function cookie()
    {
        if (func_num_args() === 0) {
            return App::get('cookie');
        }

        if (func_num_args() === 1) {
            return App::get('cookie')->get(func_get_arg(0));
        }

        return App::get('cookie', ...func_get_args());
    }
}


if (!function_exists('cache')) {
    /**
     * @return mixed
     * @throws Exception
     */
    function cache()
    {
        if (func_num_args() === 0) {
            return App::get('cache');
        }

        if (func_num_args() === 1) {
            return App::get('cache')->get(func_get_arg(0));
        }

        return App::get('cache')->put(...func_get_args());
    }
}


if (!function_exists('session')) {
    /**
     * @return mixed
     * @throws Exception
     */
    function session()
    {
        if (func_num_args() === 0) {
            return App::get('session');
        }

        if (func_num_args() === 1) {
            return App::get('session')->get(func_get_arg(0));
        }

        return App::get('session')->set(...func_get_args());
    }
}


if (!function_exists('view')) {
    /**
     * @param String $file
     * @param array $data
     * @param bool $cache
     * @return mixed
     * @throws Exception
     */
    function view(String $file, $data = [], $cache = false)
    {
        return App::get('view')->render($file, $data, $cache);
    }
}


if (!function_exists('redirect')) {
    /**
     * @param bool $link
     * @param int $refresh
     * @param int $http_response_code
     * @return mixed
     * @throws Exception
     */
    function redirect($link = false, $refresh = 0, $http_response_code = 302)
    {
        if ($link) {
            return App::get('redirect')->to($link, $refresh, $http_response_code);
        }

        return App::get('redirect');
    }
}


if (!function_exists('lang')) {
    /**
     * @param null $word
     * @param array $replace
     * @return mixed
     * @throws Exception
     */
    function lang($word = null, $replace = [])
    {
        if ($word !== null) {
            return App::get('language')->translate($word, $replace);
        }

        return App::get('language');
    }
}


if (!function_exists('validator')) {
    /**
     * @param null $data
     * @param array $rules
     * @return mixed
     * @throws Exception
     */
    function validator($data = null, $rules = [])
    {
        if ($data !== null) {
            return App::get('validator')->make($data, $rules);
        }

        return App::get('validator');
    }
}


if (!function_exists('get')) {
    function get($name = false)
    {
        return App::get('input')->get($name);
    }
}


if (!function_exists('post')) {
    function post($name = false)
    {
        return App::get('input')->post($name);
    }
}


if (!function_exists('request')) {
    function request()
    {
        if (func_num_args() === 0) {
            return App::get('request');
        }

        if (func_num_args() === 1) {
            return App::get('request')->{func_get_arg(0)};
        }

        return App::get('request')->{func_get_arg(0)} = func_get_arg(1);
    }
}


if (!function_exists('xssClean')) {
    function xssClean($data)
    {
        return App::get('input')->xssClean($data);
    }
}


if (!function_exists('fullTrim')) {
    function fullTrim($str, $char = ' '): String
    {
        return str_replace($char, '', $str);
    }
}


if (!function_exists('encode_php_tag')) {
    function encode_php_tag($str): String
    {
        return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
    }
}


if (!function_exists('preg_replace_array')) {
    function preg_replace_array($pattern, array $replacements, $subject): String
    {
        /**
         * @return mixed
         */
        $callback = static function () use (&$replacements) {
            return array_shift($replacements);
        };

        return preg_replace_callback($pattern, $callback, $subject);
    }
}


if (!function_exists('str_replace_first')) {
    function str_replace_first($search, $replace, $subject): String
    {
        return App::get('str')->replace_first($search, $replace, $subject);
    }
}


if (!function_exists('str_replace_last')) {
    function str_replace_last($search, $replace, $subject): String
    {
        return App::get('str')->replace_last($search, $replace, $subject);
    }
}


if (!function_exists('str_slug')) {
    function str_slug($str, $separator = '-'): String
    {
        return App::get('str')->slug($str, $separator);
    }
}


if (!function_exists('str_limit')) {
    function str_limit($str, $limit = 100, $end = '...'): String
    {
        return App::get('str')->limit($str, $limit, $end);
    }
}


if (!function_exists('upper')) {
    function upper(String $str, $encoding = 'UTF-8'): String
    {
        return mb_strtoupper($str, $encoding);
    }
}


if (!function_exists('lower')) {
    function lower(String $str, $encoding = 'UTF-8'): String
    {
        return mb_strtolower($str, $encoding);
    }
}


if (!function_exists('title')) {
    /**
     * @param String $str
     * @param string $encoding
     * @return string
     */
    function title(String $str, $encoding = 'UTF-8'): String
    {
        return mb_convert_case($str, MB_CASE_TITLE, $encoding);
    }
}


if (!function_exists('len')) {
    /**
     * @param array|string $value
     * @param null|string $encoding
     * @return int|bool
     */
    function len($value, $encoding = null)
    {
        if (is_string($value)) {
            return mb_strlen($value, $encoding);
        }

        if (is_array($value)) {
            return count($value);
        }

        return 0;
    }
}


if (!function_exists('str_replace_array')) {
    function str_replace_array($search, array $replace, $subject): String
    {
        return App::get('str')->replace_array($search, $replace, $subject);
    }
}


if (!function_exists('url')) {
    function url($url = null, $parameters = [])
    {
        if ($url === null) {
            return App::get('url');
        }

        return App::get('url')->to(...func_get_args());
    }
}


if (!function_exists('current_url')) {
    function current_url($url = ''): String
    {
        return App::get('url')->current($url);
    }
}


if (!function_exists('clean_url')) {
    function clean_url($url): String
    {
        if ($url === '') {
            return '';
        }

        $url = str_replace(array('http://', 'https://'), '', strtolower($url));

        if (strpos($url, 'www.') === 0) {
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
     * @return mixed
     * @throws Exception
     */
    function segment(Int $number)
    {
        return App::get('url')->segment($number);
    }
}


if (!function_exists('debug')) {
    /**
     * @param $data
     */
    function debug($data)
    {
        ob_get_clean();
        echo '<pre style="background-color:#fff; color:#222; line-height:1.2em; font-weight:normal; font:12px Monaco, Consolas, monospace; word-wrap: break-word; white-space: pre-wrap; position:relative; z-index:100000">';

        if (is_array($data)) {
            print_r($data);
        } else {
            var_dump($data);
        }
        echo '</pre>';
        die();
    }
}


if (!function_exists('is_mail')) {
    /**
     * @param String $mail
     * @return mixed
     * @throws Exception
     */
    function is_mail(String $mail)
    {
        return App::get('validator')->is_mail($mail);
    }
}


if (!function_exists('is_url')) {
    function is_url(String $url)
    {
        return App::get('validator')->is_url($url);
    }
}


if (!function_exists('is_ip')) {
    function is_ip($ip)
    {
        return App::get('validator')->is_ip($ip);
    }
}


if (!function_exists('css')) {
    function css($file, $modifiedTime = false): String
    {
        return App::get('html')->css($file, $modifiedTime);
    }
}


if (!function_exists('js')) {
    function js($file, $modifiedTime = false): String
    {
        return App::get('html')->js($file, $modifiedTime);
    }
}


if (!function_exists('img')) {
    function img($file, $attributes = []): String
    {
        return App::get('html')->img($file, $attributes);
    }
}
