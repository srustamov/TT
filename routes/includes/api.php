<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/srustamov/TT
 */


use TT\Engine\Http\Request;

//get access_token
// $this->post('api/auth/login', 'Api\AuthController@login');

$this->get('/auth/refresh', 'AuthController@refresh');

$this->get('/user', function (Request $request) {
    return response()->json($request->user());
});

$this->get('/articles', 'ArticleController@index');
$this->get('/articles/{id}', 'ArticleController@show');
