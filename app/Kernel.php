<?php namespace App;

use System\Engine\App as AppKernel;

class Kernel extends AppKernel
{
    protected $middleware = [
        \System\Engine\Http\Middleware\StartSession::class ,
        \App\Middleware\CsrfProtected::class,
        //\App\Middleware\CorsMiddleware::class,
    ];


    protected $routeMiddleware = [
        'auth'  => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'api'   => \App\Middleware\ApiMiddleware::class,
    ];
}
