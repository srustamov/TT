<?php namespace System\Engine\Http\Middleware;

use System\Engine\Load;
use System\Engine\Http\Request;

class StartSession
{
    public function handle(Request $request, \Closure $next)
    {
        if (!CONSOLE) {
            if (!Load::class('http')->isAjax()) {
                register_shutdown_function(function () {
                    Load::class('session')->set('_prev_url', Load::class('url')->current());
                });
            }

            Load::class('session')->start();
        }

        return $next($request);
    }
}
