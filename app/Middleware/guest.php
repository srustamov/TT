<?php  namespace App\Middleware;



/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/




use System\Facades\Auth;

class Guest
{

    public function handle($request, $guard)
    {
      if(Auth::guard($guard)->check())
      {
          return redirect()->back();
      }
      return true;
    }

}
