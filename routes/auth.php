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
Route::group('/auth',function($auth){
  $auth->get('/login','Auth/LoginController@showlogin');
  $auth->post('/login','Auth/LoginController@Postlogin');
  $auth->get('/register','Auth/RegisterController@showregister');
  $auth->post('/register','Auth/RegisterController@PostRegister');
  $auth->get('/logout','Auth/LoginController@logout');
});
