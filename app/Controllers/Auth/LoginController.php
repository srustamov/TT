<?php

/**
 * App\Controllers\Auth\LoginController file.
 *
 * @category Controller
 *
 * @package TT
 * @author  SamirRustamov <rustemovv96@gmail.com>
 * @link  https://github.com/srustamov/TT
 */

namespace App\Controllers\Auth;


use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Validator;
use System\Facades\Redirect;
use System\Libraries\Auth\Authentication;

class LoginController extends Controller
{



    function __construct()
    {
        $this->middleware('guest|logout');
    }


    /**
    * LoginController show method.Show login form page
    *
    * @return \System\Libraries\View\View
    */
    public function show()
    {
        return view('auth.login');
    }


    /**
    * LoginController login method.
    * Validate post data and Authentication attempt
    *
    * @param \System\Engine\Http\Request
    * @param Authentication
    *
    * @return \System\Libraries\Redirect
    */
    public function login(Request $request,Authentication $auth)
    {
        $validation =  Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|min:6'
            ]);


        if (!$validation->check())
        {
            return Redirect::url('auth/login')->withErrors($validation->messages());
        }
        else
        {
            if ($auth->attempt($request->only('email', 'password'), $request->remember))
            {
                return Redirect::url('home');
            }
            else
            {
                return Redirect::url('auth/login')->withErrors('login' , $auth->getMessage());
            }
        }
    }


    /**
    * LoginController logout method.Logout Authenticate User and redirect Home page
    *
    * @param Authentication
    *
    * @return \System\Libraries\Redirect
    */
    public function logout(Authentication $auth)
    {
        return $auth->guard('user')->logout()->redirect('/home');
    }
}
