<?php namespace App\Controllers\Auth;

use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Validator;
use System\Libraries\Auth\Authentication;

class LoginController extends Controller
{



    function __construct()
    {
        $this->middleware('guest|logout');
    }



    public function showlogin()
    {
        return view('auth.login');
    }



    public function login(Request $request,Authentication $auth)
    {
        $validation =  Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|min:6'
            ]);



        if (!$validation->check())
        {
            return redirect('auth/login')->withErrors($validation->messages());
        }
        else
        {
            if ($auth->attempt($request->only('email', 'password'), $request->remember))
            {
                return redirect('home');
            }
            else
            {
                return redirect('auth/login')->withErrors(['login' => $auth->getMessage()]);
            }
        }
    }



    public function logout(Authentication $auth)
    {
        return $auth->guard('user')->logout()->redirect('/home');
    }
}
