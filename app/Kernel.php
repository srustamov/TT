<?php namespace App;

use System\Engine\App;

class Kernel extends App
{
    protected $middleware = [
        \App\Middleware\OverrideXPoweredBy::class ,
        \App\Middleware\TrimString::class ,
        \App\Middleware\StartSession::class ,
        \App\Middleware\CsrfProtected::class,
        //\App\Middleware\CorsMiddleware::class,
        //\App\Middleware\MaintenanceMode::class,
    ];


    protected $routeMiddleware = [
        'auth'  => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'api'   => \App\Middleware\ApiMiddleware::class,
    ];



    /*
    public function __construct(...$args)
    {
        parent::__construct(...$args);

        //before bootstrapping

        #code...

        //example
        $this->setEnvFile('.env');
    }
    */
}
