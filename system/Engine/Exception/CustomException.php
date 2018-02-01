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

        $_debug = setting('APP_DEBUG',false);

        if($_debug && strtolower($_debug) == 'true')
        {
          ob_get_clean();
          
          if(!InConsole())
          {
            if(file_exists(SYSDIR . 'Engine/Exception/views/exception.php'))
            {
              return require_once(SYSDIR . 'Engine/Exception/views/exception.php');
            }
          }
          else
          {
            echo $e->getMessage();
          }

        }
        else
        {
          http_response_code( 500 );
        }

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
