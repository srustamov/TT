<?php  namespace App\Middleware;

use System\Engine\Http\Request;
use System\Facades\Response;
use System\Facades\Config;
use App;

class CorsMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        Config::set('app.debug',false);

        Response::withHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET, PUT, POST, DELETE, OPTIONS',
            'Access-Control-Max-Age' =>  '0',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, X-Auth-Token , Authorization',
        ]);

        if($request->isMethod('OPTIONS'))
        {
            Response::send();

            App::end();
        } 


        return $next($request);
    }
}
