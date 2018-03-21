<?php namespace System\Exceptions;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */
//-------------------------------------------------------------


use System\Engine\Cli\PrintConsole;
use System\Facades\Load;


class TTException
{

    private function show($e)
    {
        $this->writeErrorLog($e);

        if (Load::config('config.debug') === true)
        {
            ob_get_clean();

            if (!InConsole())
            {

                $view_file =  __DIR__.'/resource/exception.php';

                if(file_exists($view_file))
                {
                    require_once $view_file;
                }
                else
                {
                    echo $e->getMessage();
                }

            }
            else
            {
                new PrintConsole('error',"\n\n".$e->getMessage()."\n\n");
            }
        }
        else
        {
            return abort(500);
        }
    }



    public function handler($e)
    {
         return $this->show($e);
    }


    public function writeErrorLog($e)
    {
        $file    = $e->getFile();
        $line    = $e->getLine();
        $message = $e->getMessage();

        $date     = date('Y-m-d H:m:s');

        $log_file = path('/storage/logs/error.log');

        if (!file_exists($log_file))
        {
            touch($log_file);
        }

        file_put_contents($log_file, "[{$date}] File:{$file} |Message:{$message} |Line:{$line}\n", FILE_APPEND);
    }


    public static function register()
    {
      $instance = new static();

      error_reporting(-1);

      set_error_handler([$instance, 'handleError']);

      set_exception_handler([$instance, 'handler']);

      register_shutdown_function([$instance, 'handleShutdown']);

      ini_set('display_errors', 'Off');

    }


    public function handleError($level, $message, $file = '', $line = 0)
    {
        if (error_reporting() & $level)
        {
            $e = new class
            {
                private $data;
                public function setExceptionData($data){ $this->data = $data;}
                public function getFile(){ return $this->data['file']; }
                public function getMessage(){ return $this->data['message']; }
                public function getLine(){ return $this->data['line']; }
                public function getCode(){ return $this->data['code']; }
            };

            $e->setExceptionData(array(
              'file' => $file,
              'message' => $message,
              'line' => $line,
              'code' => $level
            ));

            return $this->show($e);
        }
    }


    public function handleShutdown()
    {
        if (! is_null($error = error_get_last())) {
          if (in_array($error['type'], [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE])) {
            if (!InConsole() && file_exists(__DIR__.'/resource/fatalalert.php')) {
              require_once __DIR__.'/resource/fatalalert.php';
            }
          }
        }
    }
}
