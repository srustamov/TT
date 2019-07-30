<?php namespace System\Engine;

use System\Exceptions\TTException;

class RegisterExceptionHandler
{

    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function handle()
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

        
    }
}
