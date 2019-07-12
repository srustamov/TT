<?php


namespace System\Engine\Http\Middleware;

use System\Engine\Http\Request;
use System\Exceptions\TTException;
use System\Engine\Load;

class RegisterExceptionHandler
{
    public function handle(Request $request, \Closure $next)
    {
        $isDev = Load::class('config')->get('app.debug');

        if (!CONSOLE && $isDev) {
            $whoops = new \Whoops\Run;

            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

            $whoops->register();
        } else {
            $exception = new TTException;

            $exception->register();
        }

        return $next($request);
    }
}
