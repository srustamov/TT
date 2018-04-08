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



Route::get('/','WelcomeController@index')->name('welcome');

Route::get('/home','HomeController@index')->name('home');

Route::get(array('path'=>'/language/{lang}','pattern' => ['lang'=>'[a-z]{2}'],'name'=>'lang'),'HomeController@language');

Route::get(['path'=>'/auth/logout','middleware'=>'auth','name'=>'logout'],'Auth/LoginController@logout');

Route::group(['prefix' => '/auth','middleware' => 'guest'],function(){
    Route::get('/login','Auth/LoginController@show')->name('login');
    Route::post('/login','Auth/LoginController@login');
    Route::get('/register','Auth/RegisterController@show')->name('register');
    Route::post('/register','Auth/RegisterController@register');
});
