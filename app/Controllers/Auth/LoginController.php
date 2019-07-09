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

class LoginController extends Authentication
{


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
    * @return \System\Libraries\Redirect
    */
    public function login(Request $request)
    { 

        $validation =  Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|min:6'
            ]);

        if (!$validation->check())
        {
            return Redirect::to('/auth/login')->withErrors($validation->messages());
        }
        else
        {
            if ($this->attempt($request->only('email', 'password'), $request->remember))
            {
                return Redirect::to('/home');
            }
            else
            {
                return Redirect::to('/auth/login')->withErrors('login' , $this->getMessage());
            }
        }
    }


    /**
    * LoginController logout method.Logout Authenticate User and redirect Home page
    *
    * @return \System\Libraries\Redirect
    */
    public function logout()
    {
        return $this->logoutUser()->redirect()->route('home');
    }
}
