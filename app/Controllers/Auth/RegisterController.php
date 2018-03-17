<?php namespace App\Controllers\Auth;

use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Validator;
use System\Facades\Hash;
use App\Models\User;



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
                'email'    => 'required|email|unique:users',
                'password' => 'required|min:6',
                'name'     => 'required|min:5|unique:users',
            ]);

        if (!$validation->check())
        {
            return redirect('/auth/register')->withErrors(Validator::messages());
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
                return redirect('auth/login')->with('register', 'Register successfully');
            }
            else
            {
                return redirect('auth/register')->with('register' , 'User register error occurred.Please try again');
            }
        }
    }
}
