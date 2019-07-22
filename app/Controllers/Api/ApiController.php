<?php  namespace App\Controllers\Api;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Response;
use System\Facades\Validator;
use System\Facades\Hash;
use System\Facades\OpenSsl;
use App\Models\User;

class ApiController extends Controller
{
    public function user(Request $request)
    {
        return Response::json(
            $this->transform($request->user())
        );
    }



    public function create(Request $request)
    {
        $validation =  Validator::make($request->all(), [
          'email'    => 'required|email|unique:users',
          'password' => 'required|min:6',
          'name'     => 'required|min:5|unique:users',
        ]);

      


        if (!$validation->check()) {
            return Response::setStatusCode(400)->json([
                'message' => Validator::messages(),
                'created' => fasle
                ]);
        } else {
            $token = bin2hex(OpenSsl::random(64));

            $create = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'api_token' => $token
            ]);

            if ($create) {
                return Response::setStatusCode(201)->json([
                    'message' =>'User create successfully',
                    'created' => true,
                    'auth_token' => $token
                ]);
            } else {
                return Response::json([
                    'message'=>'User register error occurred.Please try again',
                    'created' => false
                ],417);
            }
        }
    }


    public function transform($user)
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status ? 'active' : 'inactive',
            'created_at' => $user->created_at
        ];
    }
}
