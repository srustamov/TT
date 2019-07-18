<?php  namespace App\Middleware;

use System\Libraries\Arr;
use System\Facades\Response;
use System\Facades\Auth;
use App\Models\User;
use App;

/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/


class ApiMiddleware
{
    public function handle($request, \Closure $next)
    {
        $token = $this->getAuthToken($request);
        
        if ($token) {
            $user = User::where(['api_token' => $token])->first();

            if ($user) {
                Auth::user($user);
                
                return $next($request);
            } else {
                Response::setStatusCode(401)->json(['error' => 'Authentication token incorrect !'])->send();
            }
        } else {
            Response::setStatusCode(401)->json(['error' => 'Authentication token required!'])->send();
            exit;
        }

        App::end();

    }



    protected function getAuthToken($request)
    {

        $token = $request->headers->get('X-Auth-Token');

        if (!$token) {
            $token = $request->get('auth_token') ?:$request->get('token');
        }

        return $token;
    }
}
