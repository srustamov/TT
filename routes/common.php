<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/srustamov/TT
 */


$this->get('/', function () {
  return file_get_contents(
    app_path('Views/welcome.html')
  );
})->name('welcome');

// $this->get('/','WelcomeController@index')->name('welcome');

$this->get('/home/', 'HomeController@home')->name('home');

$this->get('/language/{lang}/', 'LanguageController@change')
  ->name('lang')
  ->pattern(['lang' => '[a-z]{2}']);

$this->get('/auth/logout', 'Auth/LoginController@logout')
  ->middleware('auth')
  ->name('logout');

$this->namespace('App\\Controllers\\Auth')
  ->prefix('/auth')
  ->middleware('guest')
  ->group(function () {
    $this->get('/login/', 'LoginController@show')->name('login');
    $this->post('/login/', 'LoginController@login');
    $this->get('/register/', 'RegisterController@show')->name('register');
    $this->post('/register/', 'RegisterController@register');
  });



/*
* sub domain example
$this->domain('admin.example.com')->group(function(){
    // admin.example.com
    Route::get('/','Backend/DashbaordController@index');
});
*/
