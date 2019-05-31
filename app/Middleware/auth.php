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

    public function handle($request,\Closure $next,$guard)
    {
        if(Authentication::guard($guard)->guest())
        {
            if($guard === 'admin')
            {
              return Redirect::route('welcome')->withErrors('auth', 'Bu səhifəyə icazəniz yoxdur');
            }
            else
            {
              return Redirect::route('login')->withErrors('auth', 'Öncə giriş etməlisiz');
            }
        }

        return $next($request);
    }

}
