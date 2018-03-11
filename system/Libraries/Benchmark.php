<?php namespace System\Libraries;

/**
 * @package TT
 * @author Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category  Benchmark
 */

use Windwalker\Edge\Loader\EdgeFileLoader;
use Windwalker\Edge\Edge;
use Windwalker\Edge\Cache\EdgeFileCache;

class Benchmark
{


  private static $instance;



  public function loadTime($finish,$start = APP_START):String
  {
      return round( $finish - $start, 4 );
  }


  public function countRequiredFiles():Int
  {
      return count(get_required_files());
  }


  private function server($key)
  {
      return $_SERVER[strtoupper($key)] ??  false;
  }



  private function getBenchMarkTableData($finish,$start ):Array
  {
      $data = array(
          'Load time'        => $this->loadTime($finish,$start)." seconds",
          'Memory usage'     => (int) (memory_get_usage()/1024)." kb",
          'Peak Memory usage'=> (int) (memory_get_peak_usage()/1024)." kb",
          'Load files'       => $this->countRequiredFiles(),
          'Controller'       => $this->server('called_controller'),
          'Action'           => $this->server('called_method'),
          'Request Method'   => $this->server('request_method'),
          'Request Uri'      => app('url')->request(),
          'IP'               => app('http')->ip(),
          'Document root'    => basename( $this->server('document_root')),
          'Locale'           => app('language')->locale(),
          'Protocol'         => $this->server('server_protocol'),
          'Software'         => $this->server('server_software')
      );

      return $data;

  }


  private function view($data)
  {
        $loader = new EdgeFileLoader( array( path('system/Core/view') ) );

        $edge   = new Edge( $loader , null ,
                      new EdgeFileCache(
                          config ( 'view.cache_path' )
                      )
                  );

        $content  =  $edge->render('benchmark',compact('data'));

        $content  = preg_replace('/([\n]+)|([\s]{2})/','',$content);

        return $content;
  }





  public static function show($finish,$start = APP_START)
  {
      $data = static::getInstance()->getBenchMarkTableData($finish,$start);

      echo static::getInstance()->view($data);
  }


  public static function getInstance()
  {
      if(is_null(static::$instance)) {
        static::$instance = new static();
      }

      return static::$instance;
  }

}
