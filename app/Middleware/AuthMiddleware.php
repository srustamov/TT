<?php  namespace App\Middleware;

/*
|-------------------------------------------
| Example Auth Middleware
|-------------------------------------------
*/


use Closure;
use System\Engine\Http\Request;
use System\Facades\Auth;
use System\Facades\Redirect;
use System\Facades\Response;

class AuthMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            if($request->isJson()) {
                return Response::json(['error' => 'UnAuthorized',401]);
            }
            return Redirect::route('login')->withErrors('auth', 'You must first log in');
        }

        return $next($request);
    }
}
