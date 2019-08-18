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
