<?php


namespace System\Engine\Http\Middleware;


use System\Exceptions\TTException;

class RegisterExceptionHandler
{
    public function handle()
    {
        $exception = new TTException;

        $exception->register();
    }
}