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

use Exception;
use System\Engine\Http\Request;
use System\Facades\Validator;
use System\Facades\Auth;

class LoginController
{


    /**
     * LoginController show method.Show login form page
     *
     * @throws Exception
     */
    public function show()
    {
        return view('auth.login');
    }


    /**
     * LoginController login method.
     * Validate post data and Authentication attempt
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function login(Request $request)
    {
        /**@var $validation \System\Libraries\Validator*/
        $validation =  Validator::make($request->only('email', 'password'), [
                'email'    => 'required|email',
                'password' => 'required|min:6'
            ]);

        if (!$validation->check()) {
            return redirect()->route('login')->withErrors($validation->messages());
        }

        if (Auth::attempt($request->only('email', 'password'), $request->remember)) {
            return redirect()->route('home');
        }

        return redirect()->route('login')->withErrors('login', Auth::getMessage());
    }


    /**
     * LoginController logout method.Logout Authenticate User and redirect Home page
     *
     * @return mixed
     * @throws Exception
     */
    public function logout()
    {
        Auth::logoutUser();

        return redirect()->route('home');
    }
}
