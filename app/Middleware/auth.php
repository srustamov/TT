<?php  namespace App\Middleware;



/*
|-------------------------------------------
| Example Auth Middleware
|-------------------------------------------
*/



use System\Facades\Auth as Authentication;

class Auth
{

    protected $redirect = [
        'admin' => '/',
        'user' => '/auth/login'
    ];


    public function handle($request,$guard)
    {
        if(Authentication::guard($guard)->guest())
        {
            return redirect ($this->redirect[$guard])->withErrors([ 'auth' => 'Öncə giriş etməlisiz']);
        }
    }

}
