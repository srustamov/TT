<?php namespace System\Engine\Console;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */
//-------------------------------------------------------------





if (!defined('BASEDIR'))
{
  define('BASEDIR',dirname(dirname(dirname(__DIR__))));
}

require_once("public/index.php");



use DB;

class Console
{


    private static $support;

    private static $instance;

    private static $message;





    public static function run ( array $argv )
    {
        self::$instance = new static;

        $argv = array_map(function($item)
        {
          return strtolower(trim($item));
        },
        $argv);


        if (isset( $argv[ 1 ] ))
        {
            $manage = array_slice ( $argv , '1' );
        }
        else
        {
            return self::$instance->commandList ();
        }

        self::$instance->output ();

        switch (strtolower($manage[0])) {
            case 'runserver':
                self::$instance->runServer ( $manage );
                break;
            case 'create:controller':
                self::$instance->createController ( $manage );
                break;
            case 'create:model':
                self::$instance->createModel ( $manage );
                break;
            case 'create:middleware':
                self::$instance->createMiddleware ( $manage );
                break;
            case 'session:table':
                self::$instance->createSessionTable($manage);
                break;
            case 'users:table':
                self::$instance->userTableCreate();
                break;
            case 'cache:views':
                self::$instance->clearViewCache();
                break;
            case 'cache:configs':
                self::$instance->clearConfigsCacheOrCreate($manage[1] ?? null);
                break;
            case 'key:generate':
                self::$instance->keygenerate($manage);
                break;
            default:
                self::$instance->commandList ();
                break;
        }
    }

    public function commandList ()
    {
        $this->print ( "title" , "----------------------------------------------------\n" );
        $this->print ( "title" , " Command list\n" );
        $this->print ( "title" , "----------------------------------------------------\n\n\n" );
        $this->print ( "green" , "runserver [ port(default 8000) ]\n\n" );
        $this->print ( "green" , "create:controller [Controller Name]\n\n" );
        $this->print ( "green" , "create:model [Model Name]\n\n" );
        $this->print ( "green" , "create:middleware [Middleware Name]\n\n" );
        $this->print ( "green" , "session:table --create [tableName] (Database Migration Session table) \n\n" );
        $this->print ( "green" , "users:table (Database Migration users table) \n\n" );
        $this->print ( "green" , "cache:views (Views cache file all clear)\n\n" );
        $this->print ( "green" , "cache:configs (Configs cache file all clear)\n\n" );
        $this->print ( "green" , "cache:configs --create (Configs  files all cache)\n\n" );
        $this->print ( "green" , "key:generate \n\n" );
    }


    private function print( $style , $text )
    {

        $styles = array(
            'reset' => "\033[0m" ,
            'red' => "\033[31m" ,
            'green' => "\033[32m" ,
            'yellow' => "\033[33m" ,
            'error' => "\033[37;41m" ,
            'success' => "\033[37;42m" ,
            'title' => "\033[34m" ,
        );

        if (is_null ( self::$support )) {
            if (DIRECTORY_SEPARATOR == '\\') {
                self::$support = false !== getenv ( 'ANSICON' ) || 'ON' === getenv ( 'ConEmuANSI' );
            } else {
                self::$support = function_exists ( 'posix_isatty' ) && @posix_isatty ( STDOUT );
            }
        }

        if (php_sapi_name() != 'cli') {
          self::$message .= (self::$support ? $styles[ $style ] : '' ) . $text . ( self::$support ? $styles[ 'reset' ] : '');
        } else {
          echo ( self::$support ? $styles[ $style ] : '' ) . $text . ( self::$support ? $styles[ 'reset' ] : '' );
        }

    }



    private function output ()
    {
        $this->print ( "title" , "----------------------------------------------------\n" );
        $this->print ( "title" , " OUTPUT\n" );
        $this->print ( "title" , "----------------------------------------------------\n" );
    }


    private function keygenerate()
    {
        $settings_file = BASEDIR.'/.settings';
        try
        {
          $file = fopen($settings_file,'r+');
           while (($line = fgets($file,4096)) !==false)
           {
             if(strpos($line,'APP_KEY') !== false)
             {
               $replace = $line; break;
             }
           }
           fclose($file);

           $content = file_get_contents($settings_file);

           $replace = str_replace(
                         array('+','/','?','.','[',']'),
                         array('\+',"\\/",'\?','\.','\[','\]'),
                         $replace
                     );
           $key = base64_encode(openssl_random_pseudo_bytes(40));

           $key = "APP_KEY = ".str_replace('=','',$key)."\n";

           $content = preg_replace("/{$replace}/",$key,$content);

           file_put_contents(BASEDIR.'/.settings',$content);

           return $this->print('green',$key);

        }
        catch (\Exception $e)
        {
          return $this->print('error',$e->getMessage()."\n");
        }




    }



    private function runServer ( array $manage )
    {
        if (isset( $manage[ 1 ] ) && is_numeric ( $manage[ 1 ] ))
        {
            $this->print ( 'green' , PHP_EOL . 'Php Server Run <http://localhost:' . $manage[ 1 ] . '>' . PHP_EOL . PHP_EOL );
            exec ( 'php -S localhost:' . $manage[ 1 ] . ' -t public/');
        }
        else
        {
            $this->print ( 'green' , PHP_EOL . 'Php Server Run <http://localhost:8000>' . PHP_EOL . PHP_EOL );
            exec ( 'php -S localhost:8000 -t public/' );
        }
    }



    private function createController ( array $manage )
    {
        if (isset( $manage[ 1 ] )) {
            if (!file_exists ( 'app/Controllers/' . $manage[ 1 ] . '.php' )) {
                $controller = touch ( 'app/Controllers/' . $manage[ 1 ] . '.php' );
                $namespace = "namespace App\\Controllers";
                if (strpos ( $manage[ 1 ] , '/' )) {
                    $_C = explode ( '/' , $manage[ 1 ] );
                    $name = ucfirst ( array_pop ( $_C ) );
                    if (count ( $_C ) > 0) {
                        $namespace .= '\\' . implode ( '\\' , $_C );
                    }
                } else {
                    $name = ucfirst ( $manage[ 1 ] );
                }

                if ($controller) {
                    if ($file = fopen ( 'app/Controllers/' . $manage[ 1 ] . '.php' , 'w' )) {
                        fwrite ( $file , "<?php $namespace;   \n\n\nuse App\\Controllers\\Controller;\n\n\nclass $name extends Controller\n{\n\n\t\tpublic function index(){}\n\n}" );
                        fclose ( $file );
                    }

                    $this->print ( "success" , "\nCreate $name controller successfully\n\n" );
                }
            } else {
                $this->print ( "error" , "\nThe file was already created\n\n" );
            }
        } else {
            $this->print ( "error" , "\nPlease enter Controller name \n\n" );
        }
    }



    private function createModel ( array $manage )
    {
        if (isset( $manage[ 1 ] )) {
            if (!file_exists ( 'app/Models/' . $manage[ 1 ] . '.php' )) {
                $model = touch ( 'app/Models/' . $manage[ 1 ] . '.php' );
                $namespace = "namespace App\\Models";
                if (strpos ( $manage[ 1 ] , '/' )) {
                    $_C = explode ( '/' , $manage[ 1 ] );
                    $name = ucfirst ( array_pop ( $_C ) );
                    if (count ( $_C ) > 0) {
                        $namespace .= '\\' . implode ( '\\' , $_C );
                    }
                } else {
                    $name = ucfirst ( $manage[ 1 ] );
                }

                if ($model) {
                    if ($file = fopen ( 'app/Models/' . $manage[ 1 ] . '.php' , 'w' )) {
                        fwrite ( $file , "<?php  {$namespace};  \n\n\nuse System\\Core\\Model;\n\n\nclass {$name} extends Model\n{\n\n\t protected \$table;\n\n\t protected \$fillable =  [];\n\n\n}" );
                        fclose ( $file );
                    }
                    $this->print ( "success" , "\nCreate {$name} Model successfully\n" );
                }
            } else {
                $this->print ( "error" , "\nThe file  was already created\n\n" );
            }
        } else {
            $this->print ( "error" , "\nPlease enter Model name\n\n" );
        }
    }

    /**
     * @param array $manage
     */
    private function createMiddleware ( array $manage )
    {
        if (isset( $manage[ 1 ] )) {
            if (!file_exists ( 'app/Middleware/' . $manage[ 1 ] . '.php' )) {
                $middleware = touch ( 'app/Middleware/' . $manage[ 1 ] . '.php' );
                $namespace = "namespace App\\Middleware";
                if (strpos ( $manage[ 1 ] , '/' )) {
                    $_C = explode ( '/' , $manage[ 1 ] );
                    $name = ucfirst ( array_pop ( $_C ) );
                    if (count ( $_C ) > 0) {
                        $namespace .= '\\' . implode ( '\\' , $_C );
                    }
                } else {
                    $name = ucfirst ( $manage[ 1 ] );
                }

                if ($middleware) {
                    if ($file = fopen ( 'app/Middleware/' . $manage[ 1 ] . '.php' , 'w' )) {
                        fwrite ( $file , "<?php  {$namespace};  \n\n\nclass {$name}\n{\n\n\t public function handle(\$request,\$options){}\n\n}" );
                        fclose ( $file );
                    }
                    $this->print ( "success" , "\nCreate {$name} Middleware successfully\n" );
                }
            } else {
                $this->print ( "error" , "\nThe file  was already created\n\n" );
            }
        } else {
            $this->print ( "error" , "\nPlease enter middleware name\n\n" );
        }
    }




    private function createSessionTable($manage)
    {

        $table = false;

        if (isset($manage[1]) && $manage[1] == '--create')
        {
            if (isset($manage[2]) && !empty($manage[2]))
            {
                $table = $manage[2];
            }
        }
        if (!$table) {
            $table = config('session.table','sessions');
        }

        $result = DB::pdo()->query("CREATE TABLE IF NOT EXISTS {$table} (
                    session_id varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                    expires int(100) NOT NULL,
                    data text COLLATE utf8_unicode_ci,
                     PRIMARY KEY(session_id)
                   )
                 ");

        if (!empty(DB::error_message()))
        {
            $this->print('error',"\n".DB::error_message()."\n");
        }

        if($result) {
            $this->print('success',"\n Create session table successfully \n\n");
        } else {
            $this->print('error',"\n Create session table error occurred \n");
        }

    }

    private static function clearViewCache()
    {
      $view_cache_files = glob(BASEDIR.'/storage/cache/views/*');
      $html_cache_files = glob(BASEDIR.'/storage/cache/html/*');

      foreach ([$view_cache_files,$html_cache_files] as $readdir)
      {
        foreach($readdir as $file)
        {
            if (is_file($file))
            {
                echo "Delete: [{$file}]\n";
                @unlink($file);
            }
        }
      }

      return self::$instance->print('green',"\n\nCache clear successfully \n\n");

    }



    private static function clearConfigsCacheOrCreate($subcommand)
    {
      if($subcommand == '--create')
      {
        if(!app('cache')->has('__app_configs'))
        {
            $configsArray = [];

            foreach (glob(APPDIR.'/Config/*') as $file)
            {
                $configsArray[substr(basename($file),0,-4)] = require $file;
            }

            app('cache')->forever('__app_configs',$configsArray);
        }
        return self::$instance->print('green',"\n\nConfigs cache successfully \n\n");
      }
      else
      {
        app('cache')->forget('__app_configs');

        return self::$instance->print('green',"\n\nCache configs clear successfully \n\n");
      }
    }





    private function userTableCreate()
    {

      try {

         DB::pdo()->query("CREATE TABLE IF NOT EXISTS users (
                        id int(11) NOT NULL AUTO_INCREMENT,
                        name varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                        password varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                        email varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                        status tinyint(1) NOT NULL DEFAULT '1',
                        remember_token varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                        created_on timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        forgotten_pass_code varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
                        PRIMARY KEY (id)

                )");
        return $this->print('green',"\nusers table created successfully\n\n");

      } catch (\PDOException $e) {
        if($e->getCode() == "42S01") {
          $this->print('error',"\n\n users table or view already exists\n");
          $this->print('red',"\n \n");
        }else {
          $this->print('error',"\n\n {$e->getmessage()}\n");
          $this->print('red',"\n \n");
        }
      }

    }


    public static function command($command,$shell = false)
    {
      if($shell == true) {
          return shell_exec($command);
      }

      $command  = explode(' ',str_replace('  ', ' ',$command));
      $_argv    = array('manage');
      $_argv    = array_merge($_argv,$command);

      return  self::run($_argv);

    }




    public function printMessage()
    {
      echo self::$message;
    }


    function __destruct()
    {
      self::$message = null;
    }



}
