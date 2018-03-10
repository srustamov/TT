<?php namespace System\Core;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


class Load
{


    protected static $loaded_classes = [];

    protected static $loaded_config  = [];


    /**
     * @param $class
     * @return mixed
     * @throws \Exception
     */
    public static function class( $class)
    {
        if(isset(static::$loaded_classes[$class])) {
            return static::$loaded_classes[$class];
        } else {
            if (class_exists($class)) {
                static::$loaded_classes[$class] = new $class();
                return static::$loaded_classes[$class];
            } else {
                $app_classes = config('config.classes',[]);

                if (array_key_exists( $app_classes ,$class)) {
                    static::$loaded_classes[$class] = new $app_classes[$class]();
                    return static::$loaded_classes[$class];
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
    public static function config( $name, $default = false)
    {

        if(file_exists(path('storage/system/configs.php'))) {
            if(!empty(static::$loaded_config)) {
                static::$loaded_config = require_once path('storage/system/configs.php');
            }
        }

        if (strpos($name, '.'))
        {
            list($file, $item) = explode('.', $name);

            if (isset(static::$loaded_config[$file])) {
                return static::$loaded_config[$file][$item] ?? $default;
            }

            if (file_exists(app_dir("Config/{$file}.php")))
            {
                $config = require app_dir("Config/{$file}.php");

                static::$loaded_config[$file] = $config;

                return $config[ $item ] ?? $default;
            }
            else
            {
                throw new \Exception("Config file not found. Path : [".app_dir("Config/{$file}.php")."]");
            }
        }
        else
        {
            if(isset(static::$loaded_config[$name])) {
                return static::$loaded_config[$name];
            }

            if (file_exists(app_dir("Config/{$name}.php")))
            {
                static::$loaded_config[$name] = require app_dir("Config/{$name}.php");

                return static::$loaded_config[$name];
            }
            else
            {
                throw new \Exception("Config file not found. Path : [".app_dir("Config/{$name}.php")." ]");
            }
        }
    }

    /**
     * @param String $file
     * @return mixed
     * @throws \Exception
     */
    public static function file( String $file)
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



    public static function settingVariables()
    {
        $settingsFile = path ( 'storage/system/settings' );


        if (!file_exists ( $settingsFile ) || filemtime ( $settingsFile ) < filemtime ( path ( '.settings' ) )) {

            $_auto_detect = ini_get ( 'auto_detect_line_endings' );

            ini_set ( 'auto_detect_line_endings' , 1 );

            $lines = file ( path ( '.settings' ) , FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

            ini_set ( 'auto_detect_line_endings' , $_auto_detect );

            $_settings = [];

            foreach ($lines as $line) {
                $line = trim ( $line );

                if (isset( $line[ 0 ] ) && $line[ 0 ] === '#') {
                    continue;
                }

                if (strpos ( $line , '=' ) !== false) {
                    list( $name , $value ) = array_map ( 'trim' , explode ( '=' , $line , 2 ) );
                    $name = str_replace(['\'','"'],'',$name);
                    if (preg_match ( '/\s+/' , $value ) > 0) {
                        throw new \RuntimeException( "setting variable value containing spaces must be surrounded by quotes" );
                    }

                    if (strtolower ( $value ) == 'true') {
                        $value = true;
                    }
                    if (strtolower ( $value ) == 'false') {
                        $value = false;
                    }

                    $_settings[ $name ] = $value;
                }
            }


            foreach ($_settings as $key => $value) {

                if (strpos ( $value , '$' ) !== false) {
                    $value = preg_replace_callback ( '/\${([a-zA-Z0-9_]+)}/' ,
                        function ( $m ) use ( $_settings ) {
                            if (isset( $_settings[ $m[ 1 ] ] )) {
                                return $_settings[ $m[ 1 ] ];
                            } else {
                                return ${"$m[1]"} ?? '${' . $m[ 1 ] . '}';
                            }
                        } ,
                        $value
                    );
                }

                if(function_exists('putenv')) {
                    putenv("$key=$value");
                }
                if (function_exists('apache_setenv')) {
                    apache_setenv($key,$value);
                }

                $_ENV[ $key ] = $value;
            }
            file_put_contents ( path ( 'storage/system/settings' ) , serialize ( $_ENV ) );
        } else {

            $_settings = (array) unserialize ( file_get_contents ( path ( 'storage/system/settings' ) ) );

            foreach ($_settings as $key => $value) {
                if(function_exists('putenv')) {
                    putenv("$key=$value");
                }
                if (function_exists('apache_setenv')) {
                    apache_setenv($key,$value);
                }
                $_ENV[ $key ] = $value;
            }
        }
    }






}
