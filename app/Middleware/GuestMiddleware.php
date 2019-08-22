<?php  namespace App\Middleware;

/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/




use TT\Facades\Auth;
use TT\Facades\Redirect;

class GuestMiddleware
{
    public function handle($request, \Closure $next)
    {
        if (Auth::check()) {
            return Redirect::back();
        }

        return $next($request);
    }
}
