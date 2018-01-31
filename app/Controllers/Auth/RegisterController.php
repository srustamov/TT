<?php namespace App\Controllers\Auth;


use App\Controllers\Controller;
use System\Libraries\Request;
use Validator;
use Hash;



class RegisterController extends Controller
{

		function __construct()
		{
			$this->middleware('guest','csrf');
		}





		public function showregister()
		{
			return view('auth.register');
		}



		public function register(Request $request)
		{

			$validation =  Validator::make($$request->all(),[
				'email'    => 'required|email|unique:users|unique:admins',
				'password' => 'required|min:6',
				'name'     => 'required|min:5|unique:users',
				'password_configuration' => 'required|min:6|confirm:password'
			]);

			if($validation == false)
			{
				return redirect('/auth/register')->withError(Validator::messages());
			}
			else
			{
				if(
					User::create([
						'name' => $request->name,
						'email' => $request->email,
						'password' => Hash::make($request->password)
					])
				)
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
