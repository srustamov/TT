<?php  namespace App\Middleware;

use Closure;
use TT\Engine\Http\Request;
use TT\Facades\Config;
use TT\Facades\Route;
use TT\Facades\File;


class BenchmarkPanel
{


    protected $file = 'benchmark.html';

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

        register_shutdown_function(function(){
            $content = app('benchmark')->table(microtime(true));
            File::write(
                storage_path('system/'.$this->file),
                $content
            );
        });

        $response =  $next($request);

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


    protected function minify(string $string)
    {
        $search = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
    
        $replace = array('>','<','\\1');
    
        return preg_replace($search, $replace, $string);

    }
}
