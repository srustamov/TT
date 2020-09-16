<?php

namespace App\Controllers\Api;


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


    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        $validate = validator($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$validate->check()) {
            return response()->json([
                'success' => false,
                $validate->messages()
            ], 422);
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


    /**
     * @param $user
     * @return string
     */
    private function getAccessToken($user): string
    {
        return (string) Jwt::set('user_id', $user->id)->getToken();
    }

    /**
     * @param $credit
     * @return bool|string
     */
    private function attempt($credit)
    {
        $user = User::find(['email' => $credit['email']]);

        if ($user && Hash::check($credit['password'], $user->password)) {
            return $this->getAccessToken($user);
        }

        return false;
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function refresh(Request $request)
    {
        $token = $this->getAccessToken($request->user());

        return response()->json([
            'success' => true,
            'access_token' => $token
        ]);
    }
}
