<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */

use System\Engine\Exception\CustomException;


if (!version_compare(PHP_VERSION, 7, '>='))
{
    exit('Please upgrade to PHP version 7');
}


//------------------------------------------------
require_once(SYSDIR . 'Engine/Helpers.php');
//------------------------------------------------


switch (setting('APP_DEBUG',false))
{
    case false:
        error_reporting(0);
        ini_set('display_errors', 0);
        set_exception_handler(function ($e)
        {
            CustomException::writeLog($e);
        });
        break;
    case true:
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $whoops = new Whoops\Run;
        $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
        $whoops->register();
        break;
    default:
        error_reporting(0);
        ini_set('display_errors', 0);
        set_exception_handler(function ($e)
        {
            CustomException::writeLog($e);
        });
        break;
}






//------------------------------------------------
import(SYSDIR.'Core/Helpers.php');
//------------------------------------------------


//------------------------------------------------
import_dir_files(APPDIR."/Helpers");
//------------------------------------------------

setlocale(LC_ALL, config('datetime.setLocale'));

date_default_timezone_set(config('datetime.time_zone', 'UTC'));

$aliases = config('aliases',[]);

foreach ($aliases as $key => $value)
{
    class_alias($value, $key);
}



//------------------------------------------------
import_dir_files(BASEDIR.'/routes');
//------------------------------------------------





if (!InConsole())
{
     Route::init();

     if(setting('APP_DEBUG') && !request()->ajax())
     {
       echo benchmark_panel();
     }
}
