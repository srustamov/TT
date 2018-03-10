<?php


/**
* @author  Samir Rustamov <rustemovv96@gmail.com>
* @link    https://github.com/srustamov/TT
* @category System functions
*/


/**
 * @param String $class
 * @return mixed
 * @throws Exception
 */

function app(String $class)
{

    static $called_classes = [];

    $class = strtolower($class);

    if(array_key_exists($class, $called_classes)) {
        return $called_classes[$class];
    }

    $classes = config('config.classes');

    if (array_key_exists($class, $classes)) {
        $return = new $classes[$class]();
        $called_classes[$class] = $return;
        return $return;
    } else {
        throw new Exception("Class '{$class}' not found");
    }
}





/**
 * @param String $extension
 * @param Null $default
 * @return array|bool|String
 * @throws Exception
 */
function config(String $extension, $default = null)
{
    $config_cache_file = path('storage/system/configs.php');

    if (file_exists($config_cache_file)) {
        $configs = require $config_cache_file;
    } else {
        return System\Core\Load::config($extension, $default);
    }

    if (strpos($extension, '.') !== false) {
        list($file, $item) = explode('.', $extension, 2);

        if (isset($configs[$file])) {
            if (isset($configs[$file][$item])) {
                if (is_string($configs[$file][$item])) {
                    return !empty(trim($configs[$file][$item]))
                      ? $configs[$file][$item]
                      : $default;
                } else {
                    return $configs[$file][$item];
                }
            } else {
                return $default;
            }
        } else {
            throw new Exception("Config  file not found. Path :".path("app/Config/{$file}.php"));
        }
    } else {
        if (isset($configs[$extension])) {
            return $configs[$extension];
        } else {
            throw new Exception("Config  file not found. Path :".path("app/Config/{$extension}.php"));
        }
    }
}



/**
 * @param $key
 * @param null $default
 * @return String|Null
 */
function setting($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}




/**
 * @param String $file
 * @return mixed
 * @throws Exception
 */
function import(String $file)
{
    $file = str_replace(['/','\\'], DS, trim($file));

    if (file_exists($file)) {
        return require_once($file);
    } else {
        throw new Exception("File not found. Path: [ {$file} ]");
    }
}

/**
 * @param $dir
 */
function import_dir_files($dir)
{
    foreach (glob($dir."/*") as $file) {
        require $file;
    }
}

/**
 * @param string $path
 * @return string
 */
function storage_dir($path = '')
{
    return path($path, 'storage');
}


/**
 * @param string $path
 * @return string
 */
function app_dir($path = '')
{
    return path($path, 'app');
}


/**
 * @param string $path
 * @return string
 */
function system_dir($path = '')
{
    return path($path, 'system');
}


/**
 * @param string $path
 * @return string
 */
function public_dir($path = '')
{
    return PUBLIC_DIR.'/'.ltrim($path, '/');
}


/**
 * @param $path
 * @param null $path_name
 * @return string
 */
function path( $path, $path_name = null)
{
    return BASEPATH.DS.(is_null($path_name) ? '' : ltrim($path_name.DS, '/')).ltrim($path, '/');
}


/**
 * @param Int $http_code
 * @return int
 */
function abort(Int $http_code)
{
    $messages = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    $message = $messages[$http_code] ?? '';

    http_response_code($http_code);

    header("HTTP/1.1 $http_code $message");

    if (file_exists(app_dir('Views/errors/'.$http_code.'.blade.php'))) {
        view('errors.'.$http_code);
    }

    exit();
}


/**
 * @return bool
 */
function InConsole()
{
    return (php_sapi_name() == 'cli' || php_sapi_name() == 'phpdbg');
}


/**
 * @return String
 */
function csrf_token():String
{
    static $token;

    if (is_null($token)) {
        $token = app('session')->get('_token');
    }
    return $token;
}


/**
 * @return String
 */
function csrf_field():String
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '" />';
}



/**
 * @param float $start
 * @param float $finish
 * @return string
 */
function elapsed_time($start, $finish)
{
    return round( $finish -  $start, 4)." seconds";
}


/**
 * @param $finish
 * @return mixed|null
 */
function benchmark($finish = false)
{
    $finish = $finish ?: microtime(true);

    if(
        InConsole() ||
        !config('config.debug') ||
        app('http')->isAjax())
    {
        return null;
    }

    $data = array(
        'Load time'        => elapsed_time(APP_START, $finish),
        'Memory usage'     => (int) (memory_get_usage()/1024)." kb",
        'Peak Memory usage'=> (int) (memory_get_peak_usage()/1024)." kb",
        'Load files'       => count(get_required_files()),
        'Controller'       => $_SERVER['CALLED_CONTROLLER'] ?? '',
        'Action'           => $_SERVER['CALLED_METHOD'] ?? '',
        'Request Method'   => $_SERVER['REQUEST_METHOD'],
        'Request Uri'      => app('url')->request(),
        'IP'               => app('http')->ip(),
        'Document root'    => basename($_SERVER['DOCUMENT_ROOT']),
        'Locale'           => app('language')->locale(),
        'SERVER PROTOCOL'  => $_SERVER['SERVER_PROTOCOL'],
        'SERVER SOFTWARE'  => $_SERVER['SERVER_SOFTWARE'],
    );

    $loader = new Windwalker\Edge\Loader\EdgeFileLoader( array( path('system/Core/view') ) );

    $edge = new Windwalker\Edge\Edge( $loader , null ,
        new Windwalker\Edge\Cache\EdgeFileCache(
            config ( 'view.cache_path' )
        )
    );


    $content  =  $edge->render('benchmark',compact('data'));

    $content  = preg_replace('/([\n]+)|([\s]{2})/','',$content);

    return $content;

}
