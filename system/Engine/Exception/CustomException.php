<?php namespace System\Engine\Exception;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */
//-------------------------------------------------------------




class CustomException extends \Exception
{

    public function __construct($e)
    {
        parent::__construct();
        static::writeLog($e);
        import(SYSDIR . 'Engine/Exception/views/exception.php');
    }



    public static function handler($e)
    {
        throw new CustomException($e);
    }




    public static function writeLog($e)
    {
      return write_error_log($e);
    }



}
