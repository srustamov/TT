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

class Guest
{

    private $except = [
        [\App\Controllers\Auth\LoginController::class, 'logout']
    ];


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
        return !(in_array(
            Route::getCurrent()->getController(true),
            $this->except
        ));
    }
}
