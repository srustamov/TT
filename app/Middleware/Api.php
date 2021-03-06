<?php

namespace App\Middleware;

use Closure;
use TT\Engine\Http\Request;
use TT\Facades\Jwt;
use TT\Facades\Auth;
use App\Models\User;

/*
|-------------------------------------------
| Example Api Middleware
|-------------------------------------------
*/



class Api
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $this->getAuthToken($request);
        if ($token) {
            $jwt = Jwt::make($token);
            if (
                $jwt->validate() &&
                ($user_id = $jwt->get('user_id')) &&
                ($user = User::find($user_id))
            ) {
                Auth::login($user);
            }
        }
        /*
        else {
            Response::json(['error' => 'Authentication token required!'],401)->send();
            $request->app()->end();
        }
       */
        return $next($request);
    }


    /**
     * @param Request $request
     * @return mixed
     */
    protected function getAuthToken(Request $request)
    {
        return $request->bearerToken() ??
            $request->headers->get('X-Auth-Token', null) ??
            $request->get('token');
    }
}
