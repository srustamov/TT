<?php namespace App\Middleware;

use System\Engine\Load;
use System\Engine\Http\Request;

class StartSession
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!CONSOLE) {
            if (!Load::class('http')->isAjax()) {
                register_shutdown_function(static function () {
                    Load::class('session')->set('_prev_url', Load::class('url')->current());
                });
            }
            Load::class('session')->start();
        }

        return $next($request);
    }
}
