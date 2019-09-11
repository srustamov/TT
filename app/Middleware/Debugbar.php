<?php  namespace App\Middleware;

use Closure;
use TT\Engine\Http\Request;
use TT\Facades\Config;
use TT\Facades\Route;
use TT\Facades\File;


class Debugbar
{


    protected $file = 'debugbar.html';

    protected $url = '/app-benchmark-data';


    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!CONSOLE){
            $this->registerRoute();
        }

        if(!$this->check($request)) {
            return $next($request);
        }

<<<<<<< HEAD:app/Middleware/Debugbar.php
        register_shutdown_function(function() use ($request){
            $data = $this->getData($request);
            $content = view('framework.debugbar', compact('data'))->getContent();
=======
        register_shutdown_function(function(){
            $content = app('benchmark')->table(microtime(true));
>>>>>>> e1fdcb8a0e2fd2f53f24194e144aad11dfc2dda6:app/Middleware/BenchmarkPanel.php
            File::write(
                storage_path('system/'.$this->file),
                $content
            );
        });

        $response = $next($request);

        $response->prependContent($this->getScript());

        return $response;
    }


    protected function check($request)
    {
       return !(
            CONSOLE ||
            !Config::get('app.debug') ||
            $request->url() === $this->url || 
            $request->ajax() ||
            $request->isJson() 
        );
    }


    protected function registerRoute()
    {
        Route::get($this->url,function(){

            $content =  File::get(
                storage_path('system/'.$this->file)
            );

            File::delete(storage_path('system/' . $this->file));

            return $content;
        });
    }


    protected function getScript()
    {
        $script = '
            <script defer>
                setTimeout(function() {
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var benchmark_element = document.createElement("div");
                            benchmark_element.innerHTML = this.responseText;
                            document.body.appendChild(benchmark_element);
                            var bscript = document.getElementById("app-benchmark-panel-script");
                            var bscript_new = document.createElement("script");
                            bscript_new.innerHTML = bscript.innerHTML;
                            document.body.appendChild(bscript_new);
                            bscript.parentNode.removeChild(bscript);
                        }
                    };
                    xhttp.open("GET", "'.$this->url.'", true);
                    xhttp.send();
                },1000);
            </script>';

        return $this->minify($script).PHP_EOL;

    }


    protected function getData(Request $request)
    {
        $data = array(
            'time'             => round(microtime(true) - APP_START, 4) . " s",
            'memory-usage'     => (int) (memory_get_usage() / 1024) . " kb",
            'peak-memory-usage'=> (int) (memory_get_peak_usage() / 1024) . " kb",
            'load-files'       => count(get_required_files()) - 1,
            'request-method'   => $request->server('request_method'),
            'url'              => $request->url(),
            'ip'               => $request->ip(),
            'document-root'    => basename($request->server('document_root')),
            'locale'           => $request->app('language')->locale(),
            'protocol'         => $request->server('server_protocol'),
            'software'         => $request->server('server_software')
        );

        if(defined('CONTROLLER')) {
            $data['controller'] = CONTROLLER;
            $data['action']     = defined('ACTION') ? ACTION : null;
        }

        return $data;
    }


    protected function minify(string $string)
    {
        $search = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
    
        $replace = array('>','<','\\1');
    
        return preg_replace($search, $replace, $string);

    }
}
