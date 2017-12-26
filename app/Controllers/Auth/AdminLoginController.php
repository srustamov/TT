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



		public function PostLogin(Request $request)
		{
			$post_data  = $request->all();


			$validation =  Validator::make($post_data,[
				'email'    => 'required|email',
				'password' => 'required|min:6'
			]);
			if($validation == false) {
				return redirect('admin/login')->withError(Validator::messages());
			}else {
				$control = Auth::guard('admin')->attempt([
					'email' => $post_data['email'],
					'password' => $post_data['password']
				  ],$request->post('remember'));

					if($control->user){
						return redirect('admin/dashboard');
					}elseif ($control->many_attempts) {
						return redirect('admin/login')->withError([
							'login_incorrect' => 'Cox giris cehdi.'.$control->many_attempts.' saniye sonra yeniden cehd edin']);
					}else{
						return redirect('admin/login')->withError(['login_incorrect' =>'Login or password incorrect']);
					}
				}

		}



		public function logout()
		{
			Auth::guard('admin')->logout();
		  return redirect()->back();
		}


}
