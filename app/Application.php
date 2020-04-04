<?php

namespace App;

use TT\Engine\App;
use TT\Engine\Cli\Console;

class Application extends App
{
    // application bootstrapping middleware
    protected $middleware = [
        // \App\Middleware\MaintenanceMode::class,
        // \App\Middleware\TrimString::class,
        \App\Middleware\DebugBar::class,
    ];

    protected $routeMiddleware = [
        'start_session' => \App\Middleware\StartSession::class,
        'cors' => \App\Middleware\Cors::class,
        'csrf' => \App\Middleware\CSRF::class,
        'auth' => \App\Middleware\Authentication::class,
        'guest' => \App\Middleware\Guest::class,
        'api' => \App\Middleware\Api::class,
    ];


    /*
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        #before bootstrapping
        #...code
        //$this->paths['envFile'] = '.env';
    }
     */

    protected function afterBootstrap(): void
    {
        if (inConsole()) {
            Console::setCommand([
                \App\Console\JwtSecretCommand::class,
            ]);
        }

        // example singleton
        // $this->singleton('myClass',function($app){
        //     return new MyClass();
        // });
    }
}
