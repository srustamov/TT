<?php  namespace App\Middleware;

use Closure;
use System\Engine\Http\Request;
use System\Facades\Jwt;
use System\Facades\Response;
use System\Facades\Config;
use System\Facades\Auth;
use App\Models\User;

/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/



class ApiMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Config::set('app.debug', false);

        $token = $this->getAuthToken($request);

        if ($token) {
            /**@var \System\Libraries\Auth\Jwt $jwt*/
            $jwt = Jwt::make($token);

            if($jwt->validate()) {
                if($user_id = $jwt->get('user_id')) {
                    if ($user = User::find($user_id)) {
                        Auth::user($user);
                        return $next($request);
                    }
                }
            }

            Response::json(['error' => 'Authentication token incorrect !'],401)->send();
        } else {
            Response::json(['error' => 'Authentication token required!'],401)->send();
        }

        $request->app()->end();
    }


    /**
     * @param Request $request
     * @return mixed
     */
    protected function getAuthToken(Request $request)
    {
        return $request->bearerToken() ??
               $request->headers->get('X-Auth-Token',null) ??
               $request->get('token');
    }
}
