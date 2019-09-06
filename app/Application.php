<?php namespace App;

use TT\Engine\App;

class Application extends App
{
    protected $middleware = [
        //\App\Middleware\OverrideXPoweredBy::class ,
        //\App\Middleware\MaintenanceMode::class,
        //\App\Middleware\TrimString::class ,
        \App\Middleware\StartSession::class,
        //\App\Middleware\CsrfProtected::class,
        //\App\Middleware\CorsMiddleware::class,

        \App\Middleware\BenchmarkPanel::class,

    ];


    protected $routeMiddleware = [
        'auth' => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'api' => \App\Middleware\ApiMiddleware::class,
    ];


    /*
     public function __construct(...$args)
    {
        parent::__construct(...$args);

        #before bootstrapping

        //example

        //$this->paths['envFile'] = '.env';

        // self::register('myClass',static function(){
        //     return new MyClass();
        // });
    }
     */


}
