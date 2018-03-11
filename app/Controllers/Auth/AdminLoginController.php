<?php namespace App\Controllers\Auth;

use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Validator;
use Auth;

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



    public function login(Request $request)
    {
        $validation =  Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|min:6'
            ]);

        if (!$validation->check())
        {
            return redirect('admin/login')->withError(Validator::messages());
        }
        else
        {
            $attempt =  Auth::guard('admin')->attempt(
                $request->only('email','password'), $request->remember
            );

            if ($attempt)
            {
                return redirect('admin/dashboard');
            }
            else
            {
                return redirect('admin/login')->withError(['login_incorrect' => Auth::getMessage()]);
            }
        }
    }



    public function logout(Authentication $auth)
    {
        return $auth->guard('admin')->logout()->redirect()->back();
    }
}
