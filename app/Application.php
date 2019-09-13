<?php namespace App;

use TT\Engine\App;
use TT\Engine\Cli\Console;

class Application extends App
{
    protected $middleware = [
        //\App\Middleware\OverrideXPoweredBy::class ,
        //\App\Middleware\MaintenanceMode::class,
        //\App\Middleware\TrimString::class ,
        \App\Middleware\StartSession::class,
        //\App\Middleware\CsrfProtected::class,
        //\App\Middleware\CorsMiddleware::class,

        \App\Middleware\Debugbar::class,

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

        #...code

        //$this->paths['envFile'] = '.env';


    }
     */

    protected function afterBootstrap()
    {
        if(inConsole()) {
            Console::setCommand([
                \App\Commands\JwtSecretCommand::class,
            ]);
        }

        // self::register('myClass',function(){
        //     return new MyClass();
        // });
    }


}
