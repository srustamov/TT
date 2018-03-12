<?php  namespace App\Middleware;



/*
|-------------------------------------------
| Example Auth Middleware
|-------------------------------------------
*/



use System\Facades\Auth as Authentication;

class Auth
{

    protected $redirect = [
        'admin' => '/',
        'user' => '/auth/login'
    ];


    public function handle($request,\Closure $next,$guard)
    {
        if(Authentication::guard($guard)->guest())
        {
            return $next(redirect ('/')->withErrors('auth', 'Öncə giriş etməlisiz'));
        }


    }

}
