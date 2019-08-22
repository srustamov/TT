<?php

/**
 * App\Controllers\Auth\RegisterController file.
 *
 * @category Controller
 *
 * @package TT
 * @author  SamirRustamov <rustemovv96@gmail.com>
 * @link  https://github.com/srustamov/TT
 */

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use TT\Engine\Http\Request;
use TT\Facades\Validator;
use TT\Facades\Redirect;
use TT\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{


    /**
     * RegisterController show method.Show register form page
     *
     * @throws \Exception
     */
    public function show()
    {
        return view('auth.register');
    }


    /**
     * RegisterController register method.Post data validate and Create user
     *
     * @param Request $request
     * @return \TT\Libraries\Redirect
     * @throws \Exception
     */
    public function register(Request $request)
    {
        /**@var $validation \TT\Libraries\Validator*/
        $validation =  Validator::make($request->all(), [
                'email'    => 'required|email|unique:users',
                'password' => 'required|min:6',
                'name'     => 'required|min:5|unique:users',
            ]);

        if (!$validation->check()) {
            return redirect()->route('register')->withErrors(Validator::messages());
        }

        $create = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if ($create) {
            return redirect()->route('login')->with('register', 'Register successfully');
        }

        return redirect()->route('register')->with('register', 'User register error occurred.Please try again');
    }
}
