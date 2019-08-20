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
     * @param Request $request
     * @param Closure $next
     * @param $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next,$guard)
    {
        if (!Auth::check()) {
            if($request->isJson() || $guard === 'api') {
                Response::json(['error' => 'unAuthorized'],401)->send();
                $request->app()->end();
            }
            return Redirect::route('login')->withErrors('auth', 'You must first log in');
        }

        return $next($request);
    }
}
