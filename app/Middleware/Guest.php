<?php

namespace App\Middleware;

/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/


use Closure;
use TT\Facades\Auth;
use TT\Facades\Redirect;
use TT\Engine\Http\Request;
use TT\Facades\Response;
use TT\Facades\Route;
use App\Controllers\Auth\LoginController;

class Guest
{


    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->checkExcept() && Auth::check()) {
            if ($request->isJson()) {
                Response::json(['error' => '503 you are already authenticated'])->send();
                $request->app()->end();
            }
            return Redirect::back();
        }

        return $next($request);
    }



    private function checkExcept()
    {
        if($route = Route::getCurrent()->getController(true)) {

            [$controller,$method] = $route;

            return !($controller == LoginController::class && $method == 'logout');
        }

        return true;
    }
}
