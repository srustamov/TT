<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/SamirRustamov/TT
 */

use System\Engine\Console\Console;



/*
|---------------------------------------------
|  Web Routes
|---------------------------------------------
*/


Route::get('/admin/login','Auth/AdminLoginController@showlogin');

Route::post('/admin/login','Auth/AdminLoginController@login');

Route::get('/admin','Backend/AdminController@dashboard')->middleware('auth:admin');
