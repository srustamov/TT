<?php namespace App;
use System\Exceptions\NotFoundException;



class Kernel
{

    public $middleware = [
        Middleware\CsrfProtected::class,
    ];


    public function boot()
    {
    }
}
