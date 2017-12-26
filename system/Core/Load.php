<?php namespace System\Core;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */


class Load
{


    protected static $_config_file_name;

    protected static $_config;

    protected static $settings;



    public static function config($name, $default = false)
    {
        if (strpos($name, '.'))
        {
            list($file, $item) = explode('.', $name);

            if (static::$_config_file_name == $file)
            {
                return static::$_config[ $item ] ?? $default;
            }
            else
            {
                if (file_exists(APPDIR."Config/{$file}.php"))
                {
                    $config = require APPDIR."Config/{$file}.php";

                    static::$_config_file_name = $file; static::$_config = $config;
                    return $config[ $item ] ?? $default;
                }
                else
                {
                    throw new \Exception("Config file not found. Path : [".APPDIR."Config/{$file}.php]");
                }
            }
        }
        else
        {
            if (file_exists(APPDIR."Config/{$name}.php"))
            {
                return require APPDIR."Config/{$name}.php";
            }
            else
            {
                throw new \Exception("Config file not found. Path : [".APPDIR."Config/{$name}.php ]");
            }
        }
    }





    public static function file(String $file)
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



    public static function setting($item,$default = null)
    {

      if(is_null(self::$settings))
      {
        static::$settings = parse_ini_file(BASEDIR.'/.settings');
      }

      return static::$settings[$key] ?? $default;
    }



}
