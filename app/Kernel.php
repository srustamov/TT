<?php namespace App;




class Kernel
{

    public $middleware = [
        Middleware\CsrfProtected::class,
    ];


    public function boot()
    {
      
    }
}
