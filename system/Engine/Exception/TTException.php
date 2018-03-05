<?php namespace System\Engine\Exception;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */
//-------------------------------------------------------------




class TTException extends \Exception
{

    public function __construct($e)
    {

        parent::__construct();

        write_error_log($e);

        if(config('config.debug',false))
        {
          ob_get_clean();
          
          if(!InConsole())
          {
            require_once(system_dir('Engine/Exception/views/exception.php'));
          }
          else
          {
            echo $e->getMessage();
          }

        }
        else
        {
            abort(500);
        }

    }



    public static function handler($e)
    {
        throw new TTException($e);
    }






}
