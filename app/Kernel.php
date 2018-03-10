<?php namespace App;




class Kernel
{

    public $middleware = [
        \App\Middleware\CsrfToken::class,
    ];


    public function boot()
    {
        app('url')->setBase('localhost:5000');
    }
}
