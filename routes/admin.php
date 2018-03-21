<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/srustamov/TT
 */




/*
|---------------------------------------------
|  Web Routes
|---------------------------------------------
*/

Route::group('/admin',function(){

    //Route::get('/login','Auth/AdminLoginController@showlogin');

    //Route::post('/login','Auth/AdminLoginController@login');

    Route::get('/','Backend/AdminController@dashboard')->middleware('auth:admin');
});
