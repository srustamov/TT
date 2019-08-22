<?php  namespace App\Middleware;

/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/




use TT\Facades\Auth;
use TT\Facades\Redirect;
use TT\Engine\Http\Request;
use TT\Facades\Response;

class GuestMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if (Auth::check()) {
            if ($request->isJson()) {
                Response::json(['error' => '503 you are already authenticated'])->send();
                $request->app()->end();
            }
            return Redirect::back();
        }

        return $next($request);
    }
}
