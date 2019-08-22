<?php
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/srustamov/TT
 */

/*
use TT\Engine\Http\Request;
use TT\Facades\Route;

//get access_token
Route::post('api/auth/login','Api\AuthController@login');


Route::group(['prefix' => '/api','middleware' => ['api','auth:api'] ],static function (){
    //Refresh Token
    Route::get('/auth/refresh','Api\AuthController@refresh');

    Route::get('/user',function (Request $request){
        return response()->json($request->user());
    });

    Route::get('/articles','Api\ArticleController@index');
    Route::get('/articles/{id}','Api\ArticleController@show');
    # code...
});

*/

