<?php


namespace App;


class Kernel
{
    public $middleware = [
        \App\Middleware\Csrf::class,
    ];


    public function boot()
    {

    }
}