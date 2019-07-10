<?php  namespace App\Middleware;


use System\Libraries\Arr;
use System\Facades\Response;
/*
|-------------------------------------------
| Example Guest Middleware
|-------------------------------------------
*/


class Api
{

    public function handle($request, \Closure $next,$guard)
    {

        $authToken = 'anyToken';

        $token = $this->getAuthToken();

        if($token && $token === $authToken)
        {
            return $next($request);
        }
        else 
        {
            Response::setStatusCode(401)->json(['response' => 'unAuthorized'])->send();
        }

    }



    protected function getAuthToken()
    {
        $headers = getallheaders();

        return Arr::get($headers,'Auth-Token',false);
    }

}
