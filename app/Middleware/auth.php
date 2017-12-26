<?php  namespace App\Middleware;



/*
|-------------------------------------------
| Example Auth Middleware
|-------------------------------------------
*/


use System\Libraries\Request;

class Auth
{

	 protected $redirect = [
		 'admin' => '/',
		 'user' => '/auth/login'
	 ];


	 public function handle(Request $request ,$guard)
     {
       if(\System\Facades\Auth::guard($guard)->guest())
			 {
           return redirect ($this->redirect[$guard])->withError([ 'auth' => 'Öncə giriş etməlisiz']);
       }
     }

}
