<?php namespace App\Middleware;

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
            if (!$request->ajax()) {
                register_shutdown_function(static function () use ($request){
                    $request->app('session')->set('_prev_url', $request->app('url')->current());
                });
            }
            $request->app('session')->start();
        }

        return $next($request);
    }
}
