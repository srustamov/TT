<?php  namespace App\Middleware;

/*
|-------------------------------------------
| Example Auth Middleware
|-------------------------------------------
*/



use System\Facades\Auth as Authentication;
use System\Facades\Redirect;

class AuthMiddleware
{
    public function handle($request, \Closure $next)
    {
        if (Authentication::guest()) {
            return Redirect::route('login')->withErrors('auth', 'Öncə giriş etməlisiz');
        }

        return $next($request);
    }
}
