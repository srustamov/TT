<?php namespace System\Libraries\Benchmark;

/**
 * @package TT
 * @author Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category  Benchmark
 */

use System\Engine\Http\Response;
use System\Engine\App;

class Benchmark
{
    private static $instance;

    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }


    public function loadTime($finish = null, $start = APP_START): float
    {
        if ($finish === null) {
            $finish = microtime(true);
        }

        return round($finish - $start, 4);
    }


    public function countRequiredFiles():Int
    {
        return count(get_required_files());
    }


    private function server(string $key)
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
          'Request Uri'      => $this->app::get('url')->request(),
          'IP'               => $this->app::get('http')->ip(),
          'Document root'    => basename($this->server('document_root')),
          'Locale'           => $this->app::get('language')->locale(),
          'Protocol'         => $this->server('server_protocol'),
          'Software'         => $this->server('server_software')
      );

        return $data;
    }


    private function view($data)
    {
        if(!file_exists(__DIR__.'/view/benchmark.php')) {
            return null;
        }
        ob_start();

        require_once __DIR__.'/view/benchmark.php';

        $content  = ob_get_clean();

        $content  = preg_replace('/([\n]+)|([\s]{2})/', '', $content);

        return $content;
    }





    public function table($finish, $start = APP_START)
    {
        $data = $this->getBenchMarkTableData($finish, $start);

        return $this->view($data);
    }
}
