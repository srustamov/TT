<?php 

use System\Engine\Http\Request;
use System\Engine\Http\Response;
use System\Engine\App;

$this->group(['prefix' => '/api','middleware' => ['api','cors'] ],function($api)
{

    $api->get('/users',function(Response $response,App $application){

        $response->json(['users' => []])->send();

        $application->end();
        
    });


    $api->post('/users',function(Request $request,Response $response,App $application){

        $response->json($request->all())->send();

        $application->end();
    });


});