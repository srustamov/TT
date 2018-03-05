<?php namespace App\Controllers\Auth;


use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Validator;
use Auth;





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



		public function login(Request $request)
		{

			$validation =  Validator::make($request->all(),[
				'email'    => 'required|email',
				'password' => 'required|min:6'
			]);

			if($validation == false)
			{
				return redirect('auth/login')->withError(Validator::messages());
			}
			else
			{
					if(Auth::attempt($request->only('email,password'),$request->remember))
					{
						return redirect('home');
					}
					else
					{
						return redirect('auth/login')->withError(['login_incorrect' => Auth::getMessage()]);
					}
				}
		}



		public function logout()
		{
			return Auth::guard('user')->logout()->redirect()->back();
		}


}
