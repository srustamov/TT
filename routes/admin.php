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


$groupParameters = ['domain' => 'admin.framework.tt','name' => 'dashboard.','middleware' => 'auth:admin'];

Route::group($groupParameters,function(){

    Route::get('/','Backend/AdminController@dashboard')->name('home');
    // merge group parameter name [dashboard.home]

});
