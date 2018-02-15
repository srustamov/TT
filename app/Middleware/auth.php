<?php  namespace App\Middleware;



/*
|-------------------------------------------
| Example Auth Middleware
|-------------------------------------------
*/


use System\Libraries\Request;
use System\Facades\Auth as Authentication;

class Auth
{

    protected $redirect = [
        'admin' => '/',
        'user' => '/auth/login'
    ];


    public function handle(Request $request ,$guard)
    {
        if(Authentication::guard($guard)->guest())
        {
            return redirect ($this->redirect[$guard])->withError([ 'auth' => 'Öncə giriş etməlisiz']);
        }
    }

}
