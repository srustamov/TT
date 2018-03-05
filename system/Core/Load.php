<?php namespace System\Core;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */


class Load
{




    public static function config($name, $default = false)
    {
        if (strpos($name, '.'))
        {
            list($file, $item) = explode('.', $name);

            if (file_exists(app_dir("Config/{$file}.php")))
            {
                $config = require app_dir("Config/{$file}.php");

                return $config[ $item ] ?? $default;
            }
            else
            {
                show_error("Config file not found. Path : [".app_dir("Config/{$file}.php")."]");
            }
        }
        else
        {
            if (file_exists(app_dir("Config/{$name}.php")))
            {
                return require app_dir("Config/{$name}.php");
            }
            else
            {
                show_error("Config file not found. Path : [".app_dir("Config/{$name}.php")." ]");
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




}
