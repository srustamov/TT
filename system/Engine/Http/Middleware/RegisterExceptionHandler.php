<?php


namespace System\Engine\Http\Middleware;


use System\Engine\Http\Request;
use System\Exceptions\TTException;

class RegisterExceptionHandler
{
    public function handle(Request $request, \Closure $next)
    {
        $exception = new TTException;

        $exception->register();

        return $next($request);
    }
}