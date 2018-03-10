<?php namespace System\Engine\Exception;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */
//-------------------------------------------------------------


use System\Engine\Cli\PrintConsole;

class TTException extends \Exception
{
    public function __construct($e)
    {
        parent::__construct();

        $this->writeErrorLog();

        if (APP_DEBUG === true) {

            ob_get_clean();

            if (!(php_sapi_name() == 'cli' || php_sapi_name() == 'phpdbg')) {

                $view_file =  __DIR__.'/resource/exception.php';

                if(file_exists($view_file)) {
                    require_once $view_file;
                } else {
                    echo $this->getMessage();
                }

            } else {
                new PrintConsole('error',"\n\n".$this->getMessage()."\n\n");
            }
        } else {
            abort(500);
        }
    }



    public static function handler($e)
    {
        throw new TTException($e);
    }


    public function writeErrorLog()
    {
        $file    = $this->getFile();
        $line    = $this->getLine();
        $message = $this->getMessage();

        $date     = date('Y-m-d H:m:s');

        $log_file = BASEPATH.'/storage/logs/error.log';

        if (!file_exists($log_file)) {
            touch($log_file);
        }

        file_put_contents($log_file, "[{$date}] File:{$file} |Message:{$message} |Line:{$line}\n", FILE_APPEND);
    }
}
