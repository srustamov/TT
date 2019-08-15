<?php namespace System\Exceptions;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


use System\Engine\Cli\PrintConsole;

class TTException
{
    private function show($e)
    {
        $this->writeErrorLog($e);

        if (CONSOLE) {
            return new PrintConsole('red', "\n File:".$e->getFile()."\n Line:".$e->getLine()."\n".$e->getMessage()."\n");
        }

        return abort(500);
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

        $log_file = path('/storage/logs/errors/'.date('Y-m-d').'.log');

        if (!file_exists($log_file)) {
            touch($log_file);

            chmod($log_file, 0755);
        }

        $logData  = "[{$date}] File:{$file} |Message:{$message} |Line:{$line}\n";

        @file_put_contents($log_file, $logData, FILE_APPEND);
    }


    public function handleError($level, $message, $file = '', $line = 0)
    {
        if (error_reporting() & $level) {
            $e = $this->createFakeExceptionObject(array(
                  'file' => $file,
                  'message' => $message,
                  'line' => $line,
                  'code' => $level
                ));

            return $this->show($e);
        }
    }


    public function createFakeExceptionObject($data)
    {
        $e = new class {
            private $data;

            public function setExceptionData($data)
            {
                $this->data = $data;
            }

            public function getFile()
            {
                return $this->data['file'];
            }

            public function getMessage()
            {
                return $this->data['message'];
            }

            public function getLine()
            {
                return $this->data['line'];
            }

            public function getCode()
            {
                return $this->data['code'];
            }
        };

        $e->setExceptionData($data);

        return $e;
    }


    public function handleShutdown()
    {
        if (! is_null($error = error_get_last())) {
            if (in_array($error['type'], [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE])) {
                $e = $this->createFakeExceptionObject(array(
                    'file' => $error['file'] ?? '',
                    'message' => 'Fatal Error: '.$error['message'] ?? '',
                    'line' => $error['line'] ?? '',
                    'code' => 0,
               ));

                return $this->show($e);
            }
        }
    }


    public function register()
    {
        error_reporting(-1);

        set_error_handler([$this, 'handleError']);

        set_exception_handler([$this, 'handler']);

        register_shutdown_function([$this, 'handleShutdown']);

        ini_set('display_errors', 'Off');
    }
}
