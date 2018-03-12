<?php  namespace App\Middleware;



/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/




use System\Facades\Auth;

class Guest
{

    public function handle($request, \Closure $next,$guard)
    {
      
      if(Auth::guard($guard)->check())
      {
        $next(redirect()->back());
      }

    }

}
