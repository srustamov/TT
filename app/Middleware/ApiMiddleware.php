<?php  namespace App\Middleware;

use Closure;
use System\Engine\Http\Request;
use System\Facades\Response;
use System\Facades\OpenSsl;
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
            $user = User::where(['api_token' => OpenSsl::decrypt($token)])->first();

            if ($user) {
                Auth::user($user);
                return $next($request);
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
        $token = $request->headers->get('X-Auth-Token');

        if (!$token && $request->headers->has('Authorization')) {
            $authorization = $request->headers->get('Authorization');

            if (preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
                $token =  $matches[1];
            }
        }

        return $token ?: $request->get('token');
    }
}
