<?php namespace App\Controllers\Api;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use App\Controllers\Controller;
use TT\Engine\Http\Request;
use App\Models\User;
use TT\Facades\Hash;
use TT\Facades\Jwt;

class AuthController extends Controller
{


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validate = validator($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$validate->check()) {
            return response()->json([
                'success' => false,
                $validate->messages()
            ], 400);
        }


        if ($token = $this->attempt($credentials)) {
            return response()->json([
                'success' => true,
                'access_token' => $token
            ]);
        }

        return response()->json([
            'error' => 'Unauthorized'
        ], 401);


    }


    private function getAccessToken($user): string
    {
        return (string) Jwt::set('user_id', $user->id)->getToken();
    }

    private function attempt($credit)
    {
        $user = User::where('email', $credit['email'])->first();

        if ($user && Hash::check($credit['password'], $user->password)) {
            return $this->getAccessToken($user);
        }

        return false;
    }


    public function refresh(Request $request)
    {
        $token = $this->getAccessToken($request->user());

        return response()->json([
            'success' => true,
            'access_token' => $token
        ]);
    }


}
