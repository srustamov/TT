<?php namespace System\Engine\Cli;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */
//-------------------------------------------------------------


use System\Libraries\Database\Database as ConsoleDB;

class Console
{


    private static $support;

    private static $instance;

    private static $message;

    private $db;


    public static function run ( array $argv )
    {
        self::$instance = new static;


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
                self::$instance->create ( $manage );
                break;
            case 'create:model':
                self::$instance->create ( $manage );
                break;
            case 'create:middleware':
                self::$instance->create ( $manage );
                break;
                break;
            case 'create:resource':
                self::$instance->create ( $manage );
                break;
            case 'session:table':
                self::$instance->db = new ConsoleDB();
                self::$instance->createSessionTable($manage);
                break;
            case 'users:table':
                self::$instance->db = new ConsoleDB();
                self::$instance->userTableCreate();
                break;
            case 'view:cache':
                self::$instance->clearViewCache();
                break;
            case 'config:cache':
                self::$instance->clearConfigsCacheOrCreate($manage[1] ?? null);
                break;
            case 'key:generate':
                self::$instance->keyGenerate();
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
        $this->print ( "green" , "create:resource [Controller Name]\n\n" );
        $this->print ( "green" , "create:model [Model Name]\n\n" );
        $this->print ( "green" , "create:middleware [Middleware Name]\n\n" );
        $this->print ( "green" , "session:table --create [tableName] (Database Migration Session table) \n\n" );
        $this->print ( "green" , "users:table (Database Migration users table) \n\n" );
        $this->print ( "green" , "view:cache (View cache files all clear)\n\n" );
        $this->print ( "green" , "config:cache (Configs cache file all clear)\n\n" );
        $this->print ( "green" , "config:cache --create (Configs  files all cache)\n\n" );
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

        if (is_null ( self::$support ))
        {
            if (DIRECTORY_SEPARATOR == '\\')
            {
                self::$support = false !== getenv ( 'ANSICON' ) || 'ON' === getenv ( 'ConEmuANSI' );
            }
            else
            {
                self::$support = function_exists ( 'posix_isatty' ) && @posix_isatty ( STDOUT );
            }
        }

        if (php_sapi_name() != 'cli')
        {
          self::$message .= (self::$support ? $styles[ $style ] : '' ) . $text . ( self::$support ? $styles[ 'reset' ] : '');
        }
        else
        {
          echo ( self::$support ? $styles[ $style ] : '' ) . $text . ( self::$support ? $styles[ 'reset' ] : '' );
        }

    }


    private function output ()
    {
        $this->print ( "title" , "----------------------------------------------------\n" );
        $this->print ( "title" , " OUTPUT\n" );
        $this->print ( "title" , "----------------------------------------------------\n" );
    }


    private function keyGenerate()
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
          $this->print('error',$e->getMessage()."\n");
          exit;
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


    private function create($manage)
    {

      list($create,$type) = explode(':',$manage[0],2);


      $is_resource = false;

      if ($type == 'resource')
      {
        $is_resource = true;
        $type = 'controller';
      }

      $type  = ucfirst($type);

      $_type = $type;

      if(!isset($manage[1]))
      {
        $this->print ( "error" , "\nPlease enter {$type} name \n\n" ); exit();
      }

      $name = $manage[1];

      $namespace = $type == 'Middleware' ? "namespace App\\{$type}" : "namespace App\\{$type}"."s";

      if (strpos ( $name , '/' ))
      {
          $_file = explode ( '/' , $manage[ 1 ] );

          $name =  array_pop ( $_file );

          if (count ( $_file ) > 0)
          {
              $namespace .= '\\' . implode ( '\\' , $_file );
          }
      }


      switch ($type)
      {
        case 'Controller':
          $type = 'Controllers';
          if($is_resource)
          {
            $write_data =  str_replace([':namespace',':name'],[$namespace,$name],file_get_contents(__DIR__.'/resource/resource'));;
          }
          else
          {
            $write_data =  str_replace([':namespace',':name'],[$namespace,$name],file_get_contents(__DIR__.'/resource/controller'));
          }
          break;
        case 'Model':
          $write_data =  str_replace([':namespace',':name'],[$namespace,$name],file_get_contents(__DIR__.'/resource/model'));;
          $type = 'Models';
          break;
        case 'Middleware':
          $type = 'Middleware';
          $write_data = str_replace([':namespace',':name'],[$namespace,$name],file_get_contents(__DIR__.'/resource/middleware'));;
          break;
        default:
          $this->print ( "error" , "\nCreate {$type} name undefained. Please use type ['controller,model,middleware']\n\n" );
          exit();
          break;
      }


      if (!file_exists ( "app/{$type}/" . $manage[ 1 ]. '.php' ))
      {


          $_ = explode('/',$manage[1]);

          if(count($_) > 1)
          {
            array_pop($_);

            if(count($_) > 1)
            {
              $__ = $_;
              $path = '';
              foreach ($_ as $dir)
              {
                $path .= array_shift($__).'/';

                @mkdir(path($type.'/'.$path,'app'));
              }
            }
            else
            {
              @mkdir(path($type.'/'.implode('/',$_),'app'));
            }

          }

          $file = @touch ("app/{$type}/{$manage[1]}.php");

          if ($file)
          {
              try
              {
                 file_put_contents("app/{$type}/$manage[1].php", $write_data );

                 $this->print ( "success" , "\nCreate $name {$_type} successfully\n\n" );
              }
              catch (\Exception $e) {}
          }
          else
          {
              $this->print ( "success" , "\nCreate file failed\n\n" );
          }

      }
      else
      {
          $this->print ( "error" , "\nThe file was already created\n\n" );
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
        if (!$table)
        {
            $table = config('session.table','sessions');
        }

        try
        {
          $this->db->exec("CREATE TABLE IF NOT EXISTS {$table} (
                      session_id varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      expires int(100) NOT NULL,
                      data text COLLATE utf8_unicode_ci,
                       PRIMARY KEY(session_id)
                     )
                   ");
          $this->print('success',"\n Create session table successfully \n\n");
        }
        catch (\PDOException $e)
        {
          if($e->getCode() == "42S01")
          {
            $this->print('error',"\n\n {$table} table or view already exists\n");
          }
          else
          {
            $this->print('error',"\n\n {$e->getmessage()}\n");
          }

          $this->print('red',"\n \n");
        }


    }


    private  function clearViewCache()
    {

      foreach (glob(BASEDIR.'/storage/cache/views/*') as $file)
      {
          if (is_file($file))
          {
              if(@unlink($file))
              {
                  echo "Delete: [{$file}]\n";
              }
              else
              {
                  $this->print('error','Delete failed:['.$file.']');
              }

          }

      }


     $this->print('green',"\n\nCache clear successfully \n\n");

    }


    private  function clearConfigsCacheOrCreate($subCommand)
    {
      if($subCommand == '--create')
      {
        if(!file_exists(path('storage/system/configs.php')))
        {
            $configsArray = [];

            foreach (glob(APPDIR.'/Config/*.php') as $file)
            {
                $configsArray[substr(basename($file),0,-4)] = require $file;
            }

            $__file = path('storage/system/configs.php');

            file_put_contents($__file,"<?php \n\n return array(\n\n");

            $this->config_create($configsArray);

            file_put_contents($__file,");",FILE_APPEND);

        }
        $this->print('green',"\n\nConfigs cached successfully \n\n");
      }
      else
      {
        @unlink(path('storage/system/configs.php'));

        $this->print('green',"\n\nCache configs clear successfully \n\n");
      }
    }


    private function config_create($configsArray)
    {

        foreach ($configsArray as $key => $value)
        {

            if (is_array($value))
            {
                file_put_contents(path('storage/system/configs.php'),
                    "\t'".$key."' => array(\n\n",FILE_APPEND);

                $this->config_create($value);

                file_put_contents(path('storage/system/configs.php'),
                    "\t),\n\n",FILE_APPEND);

            }
            else
            {
                if (is_bool($value))
                {
                    $value = $value ? "true" : "false";
                }
                elseif (is_integer($value)) {
                }
                else
                {
                    $value = "'$value'";
                }


                if (is_numeric($key))
                {
                    file_put_contents(path('storage/system/configs.php'), "\t$value, \n\n", FILE_APPEND);
                }
                else
                {
                    file_put_contents(path('storage/system/configs.php'), "\t'".$key."' => $value, \n\n", FILE_APPEND);
                }
            }


        }
    }


    private function userTableCreate()
    {
        $sql = <<<__SQL__
                "CREATE TABLE IF NOT EXISTS users (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    name varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                    password varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                    email varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                    status tinyint(1) NOT NULL DEFAULT '1',
                    remember_token varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                    created_on timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    forgotten_pass_code varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
                    PRIMARY KEY (id))"
__SQL__;

        try
        {
            $this->db->exec($sql);

            $this->print('green',"\nUsers table created successfully\n\n");
        }
        catch (\PDOException $e)
        {
            if($e->getCode() == "42S01")
            {
                $this->print('error',"\n\n users table or view already exists\n");
                $this->print('red',"\n \n");
            }
            else
            {
                $this->print('error',"\n\n {$e->getmessage()}\n");
                $this->print('red',"\n \n");
            }
        }

    }


    public static function command($command,$shell = false)
    {
      if($shell == true)
      {
          return shell_exec($command);
      }

      $command  = explode(' ',str_replace('  ', ' ',$command));

      $_argv    = array_merge(['manage'],$command);

      return  self::run($_argv);

    }


    public function printMessage()
    {
      echo self::$message;
    }


    public function __destruct()
    {
      self::$message = null;
    }



}
