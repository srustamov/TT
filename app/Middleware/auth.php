<?php  namespace App\Middleware;



/*
|-------------------------------------------
| Example Auth Middleware
|-------------------------------------------
*/



use System\Facades\Auth as Authentication;
use System\Facades\Redirect;

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
            return $next(Redirect::to('/')->withErrors('auth', 'Öncə giriş etməlisiz'));
        }


    }

}
