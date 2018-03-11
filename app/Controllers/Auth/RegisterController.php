<?php namespace App\Controllers\Auth;

use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Validator;
use App\Models\User;
use System\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }





    public function showregister()
    {
        return view('auth.register');
    }



    public function register(Request $request)
    {
        $validation =  Validator::make($request->all(), [
                'email'    => 'required|email|unique:users|unique:admins',
                'password' => 'required|min:6',
                'name'     => 'required|min:5|unique:users',
                'password_configuration' => 'required|min:6|confirm:password'
            ]);

        if (!$validation->check())
        {
            return redirect('/auth/register')->withError(Validator::messages());
        }
        else
        {
            $create = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            if ($create)
            {
                return redirect('auth/login')->withSuccess(['register_success' => 'Register successfully']);
            }
            else
            {
                return redirect('auth/register')->withError(['error_register' =>  'User register error occurred.Please try again']);
            }
        }
    }
}
