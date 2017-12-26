<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/SamirRustamov/TT
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

Route::get('/language/{language}','HomeController@changeLanguage')->pattern(['langauge' => '[a-z]+']);





Route::get('/api/v1/user/{token}','Api/ApiController@response');
