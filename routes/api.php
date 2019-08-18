<?php
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/srustamov/TT
 */


use System\Engine\Http\Request;
use System\Facades\Route;

Route::group('/api',static function (){

    //get access_token
    Route::post('auth/login','Api\AuthController@login');

    Route::group(['middleware' => ['api'] ], static function() {
        //Refresh Token
        Route::get('/auth/refresh','Api\AuthController@refresh');


        Route::get('/user',function (Request $request){
            return response()->json($request->user());
        });
        # code...
    });
});



