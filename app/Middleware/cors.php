<?php  namespace App\Middleware;


use System\Engine\Http\Request;
use System\Facades\Response;

class Cors
{

    public function handle(Request $request, \Closure $next,$guard)
    {

        Response::withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE, OPTIONS',
            'Access-Control-Max-Age' =>  '1000',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, X-Auth-Token , Authorization',
        ]);
        

        return $next($request);
    }




}




