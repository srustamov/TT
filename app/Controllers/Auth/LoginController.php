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
use Exception;
use TT\Engine\Http\Request;
use TT\Facades\Validator;
use TT\Facades\Auth;
use TT\View\View;

class LoginController extends Controller
{


    /**
     * LoginController show method.Show login form page
     *
     * @throws Exception
     */
    public function show(): View
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
        /**@var $validation \TT\Validator*/
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
