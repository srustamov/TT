<?php namespace System\Engine;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


class Load
{


    protected static $classes = [];

    protected static $configurations = [];


    /**
     * @param String $class
     * @param array $args
     * @return mixed
     * @throws \Exception
     */
    public function class( String $class,Array $args = [])
    {
        if(strtoupper($class) == 'LOAD') {
          return $this;
        }

        if(isset(static::$classes[$class]))
        {
            return static::$classes[$class];
        }
        else
        {

            $application_classes = $this->config('config.classes',[]);

            if (array_key_exists( $class,$application_classes))
            {

                if (method_exists($class,'__construct'))
                {
                    $args = $this->getReflectionMethodParameters(
                        $application_classes[$class],$application_classes ,$args
                    );
                    static::$classes[$class] = new $application_classes[$class](...$args);
                }
                else
                {
                    static::$classes[$class] = new $application_classes[$class];
                }

                return static::$classes[$class];
            }
            else
            {
                if(strpos('\\',$class))
                {
                    $application_classes = array_flip($application_classes);

                    if(array_key_exists($class,$application_classes))
                    {
                        return $this->class($application_classes[$class],$args);
                    }
                    else
                    {
                        $instance = new $class(...$this->getReflectionMethodParameters(
                            $class,array_flip($application_classes) ,$args
                        ));
                        static::$classes[$class] = $instance;

                        return $instance;
                    }

                }
            }

        }
        throw new \Exception('Class not found ['.$class.']');
    }


    /**
     * @param $name
     * @param bool $default
     * @return bool|mixed
     * @throws \Exception
     */
    public function config( $name, $default = false)
    {

        if(file_exists(path('storage/system/configs.php')))
        {
            if (empty(static::$configurations))
            {
              static::$configurations = require_once path('storage/system/configs.php');
            }
        }

        if (strpos($name, '.'))
        {
            list($file, $item) = explode('.', $name);

            if (isset(static::$configurations[$file]))
            {
                return static::$configurations[$file][$item] ?? $default;
            }

            if (file_exists(($config_file = path("app/Config/{$file}.php"))))
            {
                $config = require_once $config_file;

                static::$configurations[$file] = $config;

                return $config[ $item ] ?? $default;
            }
            else
            {
                throw new \Exception("Config file not found. Path : [".path("app/Config/{$file}.php")."]");
            }
        }
        else
        {
            if(isset(static::$configurations[$name]))
            {
                return static::$configurations[$name];
            }

            if (file_exists(($config_file = path("app/Config/{$name}.php"))))
            {
                static::$configurations[$name] = require_once $config_file;

                return static::$configurations[$name];
            }
            else
            {
                throw new \Exception("Config file not found. Path : [".path("app/Config/{$name}.php")." ]");
            }
        }
    }

    /**
     * @param String $file
     * @return mixed
     * @throws \Exception
     */
    public function file( String $file)
    {
        $file = str_replace(['/','\\'], DS, trim($file));

        if (file_exists($file))
        {
            return require_once $file;
        }
        else
        {
            throw new \Exception("File not found. Path: [ $file ]");
        }
    }



    public function settingVariables()
    {
        $settingsFile = path ( 'storage/system/settings' );


        if (!file_exists ( $settingsFile ) || filemtime ( $settingsFile ) < filemtime ( path ( '.settings' ) ))
        {

            $_auto_detect = ini_get ( 'auto_detect_line_endings' );

            ini_set ( 'auto_detect_line_endings' , 1 );

            $lines = file ( path ( '.settings' ) , FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

            ini_set ( 'auto_detect_line_endings' , $_auto_detect );

            $_settings = [];

            foreach ($lines as $line)
            {
                $line = trim ( $line );

                if (isset( $line[ 0 ] ) && $line[ 0 ] === '#')
                {
                    continue;
                }

                if (strpos ( $line , '=' ) !== false)
                {
                    list( $name , $value ) = array_map ( 'trim' , explode ( '=' , $line , 2 ) );

                    $name = str_replace(['\'','"'],'',$name);

                    if (preg_match ( '/\s+/' , $value ) > 0)
                    {
                        throw new \RuntimeException( "setting variable value containing spaces must be surrounded by quotes" );
                    }

                    if (strtolower ( $value ) == 'true')
                    {
                        $value = true;
                    }
                    elseif (strtolower ( $value ) == 'false')
                    {
                        $value = false;
                    }


                    $_settings[ $name ] = $value;
                }
            }


            foreach ($_settings as $key => $value)
            {

                if (strpos ( $value , '$' ) !== false)
                {
                    $value = preg_replace_callback ( '/\${([a-zA-Z0-9_]+)}/' ,
                                  function ( $m ) use ( $_settings )
                                  {
                                      if (isset( $_settings[ $m[ 1 ] ] ))
                                      {
                                          return $_settings[ $m[ 1 ] ];
                                      }
                                      else
                                      {
                                          return ${"$m[1]"} ?? '${' . $m[ 1 ] . '}';
                                      }
                                  } ,
                                  $value
                              );
                }

                if(function_exists('putenv'))
                {
                    putenv("$key=$value");
                }
                if (function_exists('apache_setenv'))
                {
                    apache_setenv($key,$value);
                }

                $_ENV[ $key ] = $value;
            }
            file_put_contents ( path ( 'storage/system/settings' ) , serialize ( $_ENV ) );
        }
        else
        {

            $_settings = (array) unserialize ( file_get_contents ( path ( 'storage/system/settings' ) ) );

            foreach ($_settings as $key => $value)
            {
                if(function_exists('putenv'))
                {
                    putenv("$key=$value");
                }
                if (function_exists('apache_setenv'))
                {
                    apache_setenv($key,$value);
                }
                $_ENV[ $key ] = $value;
            }
        }
    }



    private function getReflectionMethodParameters($class_name,$application_classes,$args)
    {
        $reflection = new \ReflectionMethod($class_name, '__construct');

        foreach ($reflection->getParameters() as $num => $param)
        {

            if ($param->getClass())
            {

                $class = $param->getClass()->name;

                if(in_array($class,$application_classes))
                {
                    $args[$num] = $this->class($application_classes[$class]);
                }
                else
                {
                    $args[$num] = new $class();
                }

            }
        }
        return $args;
    }


}
