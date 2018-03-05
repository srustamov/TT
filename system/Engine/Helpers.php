<?php


/**
 * @package TT
 * @author Samir Rustamov
 * @category System functions
 *
 */



/**
 * @param String $class_name
 * @return Mixed
 */

function app(String $class_name)
{

    $classes = [
        'auth' => 'System\Libraries\Auth\Authentication',
        'db' =>  'System\Libraries\Database\Database',
        'database' =>  'System\Libraries\Database\Database',
        'openssl' =>  'System\Libraries\Encrypt\OpenSsl',
        'email' =>  'System\Libraries\Mail\Email',
        'session' =>  'System\Libraries\Session\Session',
        'array' =>  'System\Libraries\Arr',
        'cache' =>  'System\Libraries\Cache\Cache',
        'cookie' =>  'System\Libraries\Cookie',
        'file' =>  'System\Libraries\File',
        'hash' =>  'System\Libraries\Hash',
        'html' =>  'System\Libraries\Html',
        'http' =>  'System\Libraries\Http',
        'input' =>  'System\Libraries\Input',
        'language' =>  'System\Libraries\Language',
        'lang' =>  'System\Libraries\Language',
        'redirect' =>  'System\Libraries\Redirect',
        'string' =>  'System\Libraries\Str',
        'str' =>  'System\Libraries\Str',
        'validator' =>  'System\Libraries\Validator',
        'url' =>  'System\Libraries\Url',
        'view' =>  'System\Libraries\View',
        'console' =>  'System\Engine\Console\Console',
        'load' =>  'System\Core\Load',
        'exception' =>  'System\Engine\Exception\CustomException',
        'middleware' =>  'System\Engine\Http\Middleware',
        'router' =>  'System\Engine\Http\Router',
        'request' =>  'System\Engine\Http\Request',
    ];

    $class = strtolower($class_name);

    if(array_key_exists($class,$classes))
    {
        return (new $classes[$class]());
    }
    else
    {
         show_error("Class '{$class_name}' not found");
    }
}


/**
 * @param String $extension
 * @param Null $default
 * @return Bool|array|String
 */
function config( String $extension, $default = null)
{

    $config_cache_file = path('storage/system/configs.php');

    if(file_exists($config_cache_file))
    {
      $configs = require $config_cache_file;
    }
    else
    {
      return System\Core\Load::config($extension, $default);
    }

    if (strpos($extension, '.') !== false)
    {
        list($file, $item) = explode('.', $extension, 2);

        if(isset($configs[$file]))
        {
            if(isset($configs[$file][$item]))
            {

                return !empty(trim($configs[$file][$item]))
                    ? $configs[$file][$item]
                    : $default;
            }
            else
            {
                 show_error("Config file <span style=\"color:red\">{$file}</span> item <span style=\"color:red\">{$item}</span> not found");
            }
        }
        else
        {
             show_error("Config  file not found. Path :".path("app/Config/{$file}.php"));
        }

    }
    else
    {
        if(isset($configs[$extension]))
        {
            return $configs[$extension];
        }
        else
        {
            show_error("Config  file not found. Path :".path("app/Config/{$extension}.php"));
        }
    }


}



/**
 * @param $key
 * @param null $default
 * @return String|Null
 */
function setting( $key, $default = null)
{
   return $_ENV[$key] ?? $default;
}


/**
 * @param String $file
 * @return mixed
 */
function import( String $file)
{
    $file = str_replace(['/','\\'], DS, trim($file));

    if (file_exists($file))
    {
        return require_once($file);
    }
    else
    {
        show_error("File not found. Path: [ {$file} ]");
    }
}

/**
 * @param $dir
 */
function import_dir_files( $dir)
{
    foreach(glob($dir."/*") as $file)
    {
        require $file;
    }
}

/**
 * @param string $path
 * @return string
 */
function storage_dir( $path = '')
{
    return path($path,'storage');
}


/**
 * @param string $path
 * @return string
 */
function app_dir( $path = '')
{
    return path($path,'app');
}


/**
 * @param string $path
 * @return string
 */
function system_dir( $path = '')
{
    return path($path,'system');
}


/**
 * @param string $path
 * @return string
 */
function public_dir( $path = '')
{
    return PUBLIC_DIR.'/'.ltrim($path,'/');
}


function path($path,$path_name = null)
{
  return BASEDIR.DS.(is_null($path_name) ? '' : ltrim($path_name.DS,'/')).ltrim($path,'/');
}


/**
 * @param Int $http_code
 * @return int
 */
function abort( Int $http_code)
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

    if (file_exists(app_dir('Views/errors/'.$http_code.'.php')))
    {
        import(app_dir('Views/errors/'.$http_code.'.php'));
    }

    exit();
}


/**
 * @param String|Exception $message
 * @param string $file
 * @param string $line
 */
function show_error( $message, $file = 'undefained', $line = 'undefained')
{

    if(setting('APP_DEBUG',false) == true)
    {
        if($message instanceof Exception)
        {

            $_message = $message->getMessage();
            $file     = $message->getFile();
            $line     = $message->getLine();

            $file = preg_replace('/\[(.*?)\]/', '<span style="color:#990000;">$1</span>', $file);
            $line = preg_replace('/\[(.*?)\]/', '<span style="color:#990000;">$1</span>', $file);
        }
        else
        {
          $_message = $message;
        }

        $style  = 'border:solid 1px red;';
        $style .= 'background:#FEFEFE;';
        $style .= 'padding:10px;';
        $style .= 'margin-bottom:10px;';
        $style .= 'font-family:Calibri, Ebrima, Century Gothic, Consolas, Courier New, Courier, monospace, Tahoma, Arial;';
        $style .= 'color:#666;';
        $style .= 'text-align:left;';
        $style .= 'font-size:14px;';

        $_message = preg_replace('/\[(.*?)\]/', '<span style="color:#990000;">$1</span>', $_message);

        $str  = "<div style=\"$style\">";
        $str .= $_message;
        $str .= '</div>';

        exit($str);
    }
    else
    {
        write_error_log($message,$file,$line);
    }

}


/**
 * @param String|Exception $message
 * @param string $file
 * @param string $line
 */
function write_error_log( $message, $file = 'undefined', $line = 'undefined')
{

    if($message instanceof Exception)
    {
        $file    = $message->getFile();
        $line    = $message->getLine();
        $message = $message->getMessage();
    }
    $date     = date('Y-m-d H:m:s');

    $log_file = path('storage/logs/error.log');

    if(!file_exists($log_file))
    {
        touch($log_file);
    }

    @file_put_contents($log_file,"[{$date}] File:{$file} |Message:{$message} |Line:{$line}\n" ,FILE_APPEND);
}


/**
 * @return bool
 */
function InConsole()
{
    return (php_sapi_name() == 'cli' || php_sapi_name() == 'phpdbg');
}





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



function csrf_check():Bool
{

    if(!isset($_POST['_token']))
    {
      return false;
    }
    else
    {
      $post_token = $_POST['_token'];
    }

    $token = app('session')->get('csrf_token_data');

    if(!$token)
    {
      return false;
    }

    return ($token === $post_token);
}



function csrf_field():String
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '" />';
}



/**
 * @param float $start
 * @param float $finish
 * @return string
 */
function elapsed_time( $start, $finish)
{
    return (round((float) $finish - (float) $start,4)*1000)." ms";
}





function benchmark_panel()
{
  if (!InConsole())
  {
     if(setting('APP_DEBUG') && !app('http')->isAjax())
     {
       ob_start();

       $style  = 'max-height: 400px;margin:0;overflow: auto;z-index:99999999999999;';
       $style .= 'background-color: #1f1d1d;color:white;';
       $style .= 'position: fixed;bottom: 0;';
       $style .= 'padding:0 0 5px 0;box-sizing: border-box;';
       $style .= 'font-family: monospace;font-size: 14px;min-width:calc(100% - 40px);display:none';
       $array = [
           'Total time'       => elapsed_time(APP_START,microtime(true)),
           'Memory usage'     => (int) (memory_get_usage()/1024)." kb",
           'Peak Memory usage'=> (int) (memory_get_peak_usage()/1024)." kb",
           'Load files'       => count(get_required_files()),
           'Controller'       => $_SERVER['CALLED_CONTROLLER'] ?? '',
           'Action'           => $_SERVER['CALLED_METHOD'] ?? '',
           'Request Method'   => $_SERVER['REQUEST_METHOD'],
           'Request Uri'      => $_SERVER['REQUEST_URI'],
           'Document root'    => basename($_SERVER['DOCUMENT_ROOT']),
           'Locale'           => app('lang')->locale(),
           'PHP SELF'         => $_SERVER['PHP_SELF'],
           'REMOTE ADDR'      => $_SERVER['REMOTE_ADDR'],
           'REMOTE PORT'      => $_SERVER['REMOTE_PORT'],
           'SERVER PROTOCOL'  => $_SERVER['SERVER_PROTOCOL'],
           'SERVER SOFTWARE'  => $_SERVER['SERVER_SOFTWARE'],
       ];
       ?>
       <span id="__bench" style="<?php echo $style?>">
         <p title="Http status" style="background-color: #2b542c;color:white;height:30px;line-height:30px;">
           &nbsp HTTP status <?php echo http_response_code() ?>&nbsp
          <span style="padding:0 10px;float: right;color: white;font-weight: bold"><?=setting('APP_NAME','TT')?></span>
         </p>
         <p><span style="color:green"> root@<?php echo setting('APP_NAME','TT')?></span>:~<span style="color:red">#</span> benchmark</p>
         <table>
           <tr>
           <th>
             +--------------------------------------------<br />
             + Name                                       <br />
             +--------------------------------------------<br />
           </th>
           <th>
             +--------------------------------------------<br />
             + Value                                      <br />
             +--------------------------------------------<br />
           </th>
           </tr>
           <?php foreach($array as $name => $value): ?>
           <tr>
            <td>
              +<i style="color:rgb(190, 49, 3)"><?=$name?></i><br />
              +--------------------------------------------<br />
            </td>
             <td>
              + <i style="color:green"><?=$value?></i><br />
              +--------------------------------------------<br />
             </td>
           </tr>
           <?php endforeach; ?>
         </table>
        </span>
       <span  onclick="bencht(this)" style="background-color:black;color:white;width:40px;
        display:inline-block;position: fixed;bottom: 0;right: 0;
        font-size: 20px;font-weight: bold;text-align: center;cursor: pointer;line-height:40px;">B</span>
       <script>
           var bench = document.getElementById("__bench");

           if(bench.style.display != "none")
           {
               bench.nextElementSibling.style.height    = bench.offsetHeight+"px";
               bench.nextElementSibling.style.lineHeight = bench.offsetHeight+"px";
               bench.firstElementChild.style.lineHeight = bench.offsetHeight+"px";
           }
           function bencht($this)
           {
               if(bench.style.display != "none")
               {
                   $this.style.height = "40px";
                   $this.innerHTML = "B";
                   bench.style.display = "none";
               }
               else
               {
                   $this.innerHTML = "X";
                   bench.style.display = "inline-block";
                   $this.style.height = bench.offsetHeight+"px";
               }
           }
       </script>
       <?php
       $__bench = ob_get_contents();ob_end_clean();

       return $__bench;
     }
  }

  return true;


}
