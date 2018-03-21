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



Route::get('/',function(){
  return view('welcome');
});


Route::get('/home','HomeController@index');

Route::get('/language/{language}','HomeController@changeLanguage')->pattern(['langauge' => '[a-z]{2}']);


Route::group('/auth',function(){
    Route::get('/login','Auth/LoginController@show');
    Route::post('/login','Auth/LoginController@login');
    Route::get('/register','Auth/RegisterController@show');
    Route::post('/register','Auth/RegisterController@register');
    Route::get('/logout','Auth/LoginController@logout');
});
