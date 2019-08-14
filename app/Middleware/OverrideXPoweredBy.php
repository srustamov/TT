<?php namespace App\Middleware;

use System\Engine\Http\Request;

class OverrideXPoweredBy
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */

    public function handle(Request $request, \Closure $next)
    {
        $response =  $next($request);

        $response->header('X-Powered-By','TT Framework');

        return $response;
    }
}
