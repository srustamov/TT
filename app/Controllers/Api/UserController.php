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

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function user(Request $request)
    {
        return Response::json(
            $this->transform($request->user())
        );
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function users(Request $request)
    {
        $users = User::select('name,email,status,created_at')->orderBy('id', 'DESC')->limit(10)->get();
        return Response::json(
            $this->transform($users ?? [])
        );
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $validation =  Validator::make($request->all(), [
          'email'    => 'required|email|unique:users',
          'password' => 'required|min:6',
          'name'     => 'required|min:5|unique:users',
        ]);




        if (!$validation->check()) {
            return Response::setStatusCode(422)->json([
                'message' => Validator::messages(),
                'success' => false
                ]);
        }

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
                'success' => true,
                'auth_token' => OpenSsl::encrypt($token)
            ]);
        }

        return Response::json([
            'message'=>'User register error occurred.Please try again',
            'success' => false
        ], 417);
    }


    public function update($id, Request $request)
    {
        $user = User::find($id);

        if (!$user) {
            Response::setStatusCode(404)->json([
            'message' => 'User not found!',
            'success' => false
          ]);
        }

        $validation =  Validator::make($request->input->only('email', 'password', 'name'), [
          'email'    => $request->input('email') ? 'required|email|unique:users' : '',
          'password' => $request->input('password') ? 'required|min:6' : '',
          'name'     => $request->input('name') ? 'required|min:5|unique:users' :'',
        ]);

        if (!$validation->check()) {
            return Response::setStatusCode(400)->json([
                'message' => Validator::messages(),
                'success' => false
                ]);
        }

        if ($request->input('password')) {
            $request->input->set('password', Hash::make($request->input('password')));
        }


        $update = User::where('id', $id)->update($request->input->only('name', 'email', 'password'));

        if ($update) {
            return Response::setStatusCode(200)->json([
                'message' =>'User updated successfully',
                'success' => true,
            ]);
        }

        return Response::json([
            'message'=>'Failed to update user.Something went wrong',
            'success' => false
        ], 417);
    }


    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return Response::setStatusCode(404)->json([
              'message' => 'User has already been deleted or does not exist',
              'success' => true,
            ]);
        }

        if ($delete = User::destroy($id)) {
            return Response::setStatusCode(200)->json([
              'message' =>'User deleted successfully',
              'success' => true,
            ]);
        } else {
            return Response::setStatusCode(417)->json([
              'message' =>'Could not delete user. Something went wrong',
              'success' => false,
            ]);
        }
    }


    /**
     * @param object|array $user
     * @return array
     */
    public function transform($user_resource): array
    {
        $users = is_array($user_resource) ? $user_resource : [$user_resource];

        $transform = [];

        $transform['success'] = true;

        foreach ($users as $user) {
            $transform['data'][] = [
              'name' => $user->name,
              'email' => $user->email,
              'status' => $user->status ? 'active' : 'inactive',
              'created_at' => $user->created_at
          ];
        }

        $transform['version'] = '1.3.0';
        $transform['author']  = 'Samir Rustamov';
        return $transform;
    }
}
