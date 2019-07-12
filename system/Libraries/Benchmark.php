<?php namespace System\Libraries;

/**
 * @package TT
 * @author Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category  Benchmark
 */

use System\Engine\Http\Response;
use System\Engine\Load;

class Benchmark
{
    private static $instance;



    public function loadTime($finish = null, $start = APP_START)
    {
        if($finish === null) $finish = microtime(true);
        
        return round($finish - $start, 4);
    }


    public function countRequiredFiles():Int
    {
        return count(get_required_files());
    }


    private function server($key)
    {
        return $_SERVER[strtoupper($key)] ??  false;
    }



    private function getBenchMarkTableData($finish, $start):array
    {
        $data = array(
          'Load time'        => $this->loadTime($finish, $start)." seconds",
          'Memory usage'     => (int) (memory_get_usage()/1024)." kb",
          'Peak Memory usage'=> (int) (memory_get_peak_usage()/1024)." kb",
          'Load files'       => $this->countRequiredFiles(),
          'Controller'       => defined('CONTROLLER') ? CONTROLLER : null,
          'Action'           => defined('ACTION') ? ACTION : null,
          'Request Method'   => $this->server('request_method'),
          'Request Uri'      => Load::class('url')->request(),
          'IP'               => Load::class('http')->ip(),
          'Document root'    => basename($this->server('document_root')),
          'Locale'           => Load::class('language')->locale(),
          'Protocol'         => $this->server('server_protocol'),
          'Software'         => $this->server('server_software')
      );

        return $data;
    }


    private function view($data)
    {
        ob_start();

        require_once path('system/Engine/view/benchmark.php');

        $content  = ob_get_clean();

        $content  = preg_replace('/([\n]+)|([\s]{2})/', '', $content);

        return $content;
    }





    public static function table($finish, $start = APP_START)
    {
        $data = static::getInstance()->getBenchMarkTableData($finish, $start);

        return static::getInstance()->view($data);
    }


    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
