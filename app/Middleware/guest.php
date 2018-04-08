<?php  namespace App\Middleware;



/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/




use System\Facades\Auth;
use System\Facades\Redirect;

class Guest
{

    public function handle($request, \Closure $next,$guard)
    {

      if(Auth::guard($guard)->check())
      {
        return Redirect::back();
      }

      return $next($request);
    }

}
