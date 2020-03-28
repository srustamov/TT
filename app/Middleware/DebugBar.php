<?php

namespace App\Middleware;

use Closure;
use TT\Engine\Http\Request;
use TT\Facades\Config;
use TT\Facades\Route;
use TT\Facades\File;

class DebugBar
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
        if (!CONSOLE) {
            $this->registerRoute();
        }

        if (!$this->check($request)) {
            return $next($request);
        }

        $response = $next($request);

        register_shutdown_function(function () use ($request) {
            $data = $this->getData($request);
            $content = view('framework.debugbar', compact('data', 'request'))->getContent();
            File::write(
                storage_path('system/' . $this->file),
                $content
            );
        });

        $response->prependContent($this->getScript());

        return $response;
    }


    protected function check($request)
    {
        return !(CONSOLE ||
            !Config::get('app.debug') ||
            $request->url() === $this->url ||
            $request->ajax() ||
            $request->isJson());
    }


    protected function registerRoute()
    {
        Route::get($this->url, function () {

            $content =  File::get(
                storage_path('system/' . $this->file)
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
                    let $http = new XMLHttpRequest();
                    $http.onreadystatechange = function() {
                        if (this.readyState === 4 && this.status === 200) {
                            let debug_bar_element = document.createElement("div");
                            debug_bar_element.innerHTML = this.responseText;
                            document.body.appendChild(debug_bar_element);
                            let debug_bar_script = document.querySelector("script#app-debug-bar-script");
                            let debug_bar_script_new = document.createElement("script");
                            debug_bar_script_new.innerHTML = debug_bar_script.innerHTML;
                            document.body.appendChild(debug_bar_script_new);
                            debug_bar_script.parentNode.removeChild(debug_bar_script);
                        }
                    };
                    $http.open("GET", "' . $this->url . '", true);
                    $http.send();
                },1000);
            </script>';

        return $this->minify($script) . PHP_EOL;
    }


    protected function getData(Request $request)
    {
        $data = array(
            'time'             => round((microtime(true) - APP_START) * 1000) . " ms",
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

        if (defined('CONTROLLER')) {
            $data['controller'] = CONTROLLER;
            $data['action']     = defined('ACTION') ? ACTION : null;
        }

        return $data;
    }


    protected function minify(string $string)
    {
        $search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');

        $replace = array('>', '<', '\\1');

        return preg_replace($search, $replace, $string);
    }
}
