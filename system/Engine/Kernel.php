<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */
error_reporting(E_ALL);

ini_set('display_errors',0);

use System\Engine\Exception\CustomException;


if (!version_compare(PHP_VERSION, 7, '>='))
{
    exit('Please upgrade to PHP version 7');
}


//------------------------------------------------
require_once(SYSDIR.'Engine/Helpers.php');
//------------------------------------------------

set_settings_variable();


set_exception_handler(function ($e)
{
    CustomException::handler($e);
});



//------------------------------------------------
import(SYSDIR.'Core/Helpers.php');
//------------------------------------------------


//------------------------------------------------
import_dir_files(APPDIR."Helpers");
//------------------------------------------------

setlocale(LC_ALL, config('datetime.setLocale'));

date_default_timezone_set(config('datetime.time_zone', 'UTC'));


foreach (config('aliases',[]) as $key => $value)
{
    class_alias($value, $key);
}


//------------------------------------------------
import_dir_files(BASEDIR.'/routes');
//------------------------------------------------




if (!InConsole()) Route::init();
