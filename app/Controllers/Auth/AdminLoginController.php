<?php namespace App\Controllers\Auth;


use App\Controllers\Controller;
use System\Libraries\Request;
use Validator;
use Auth;


class AdminLoginController extends Controller
{

		function __construct()
		{
			$this->middleware('guest:admin|logout','csrf');
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
				return redirect('admin/login')->withError(Validator::messages());
			}
			else
			{

					if(Auth::guard('admin')->attempt(['email' => $request->email,'password' => $request->password],$request->post('remember')))
					{
						return redirect('admin/dashboard');
					}
					else
					{
						return redirect('admin/login')->withError(['login_incorrect' =>Auth::getMessage()]);
					}
				}

		}



		public function logout()
		{
			return Auth::guard('admin')->logout()->redirect()->back();
		}


}
