<?php namespace App\Controllers\Auth;

use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Validator;
use System\Libararies\Auth\Authentication;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin|logout');
    }



    public function showlogin()
    {
        return view('auth.login');
    }



    public function login(Request $request , Authentication $auth)
    {
        $validation =  Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|min:6'
            ]);

        if ($validation == false)
        {
            return redirect('admin/login')->withError(Validator::messages());
        }
        else
        {
            $attempt = $auth->guard('admin')->attempt(
                $request->only('email','password'), $request->remember
            );

            if ($attempt)
            {
                return redirect('admin/dashboard');
            }
            else
            {
                return redirect('admin/login')->withError(['login_incorrect' =>$auth->getMessage()]);
            }
        }
    }



    public function logout(Authentication $auth)
    {
        return $auth->guard('admin')->logout()->redirect()->back();
    }
}
