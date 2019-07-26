<?php  namespace App\Middleware;

/*
|-------------------------------------------
| Example Auth Middleware
|-------------------------------------------
*/


use Closure;
use System\Facades\Auth;
use System\Facades\Redirect;

class AuthMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guest()) {
            return Redirect::route('login')->withErrors('auth', 'You must first log in');
        }

        return $next($request);
    }
}
