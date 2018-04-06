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



Route::group(['domain' => 'admin.framework.tt','middleware' => 'auth:admin'],function(){

    Route::get('/','Backend/AdminController@dashboard');

});


