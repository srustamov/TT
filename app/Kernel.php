<?php namespace App;




class Kernel
{

    public $middleware = [
        \App\Middleware\CsrfProtected::class,
    ];


    public function boot(){}
}
