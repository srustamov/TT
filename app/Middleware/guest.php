<?php  namespace App\Middleware;



/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/



use System\Libraries\Request;
use Auth;

class Guest
{


    public function handle(Request $request, $guard)
    {
      if(Auth::guard($guard)->check())
	  {
          return redirect()->back();
      }
      return true;
    }

}
