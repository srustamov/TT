<?php namespace App\Controllers\Auth;


use App\Controllers\Controller;
use System\Libraries\Request;
use Validator;
use Auth;




class LoginController extends Controller
{

		function __construct()
		{
			$this->middleware('guest|logout','csrf');
		}



		public function showlogin()
		{
			return view('auth.login');
		}



		public function PostLogin(Request $request)
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
					if(Auth::attempt(['email' => $request->email,'password' => $request->password ],$request->post('remember')))
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
			Auth::guard('user')->logout();
		  return redirect()->back();
		}


}
