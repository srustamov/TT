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



$this->get('/','WelcomeController@index')->name('welcome');

$this->get('/home','HomeController@index')->name('home');

$this->name('lang')->pattern('lang','[a-z]{2}')->get('/language/{lang}','HomeController@language');

$this->get(['path'=>'/auth/logout','middleware'=>'auth','name'=>'logout'],'Auth/LoginController@logout');

$this->group(['prefix' => '/auth','middleware' => 'guest'],function()
{
    $this->get('/login','Auth/LoginController@show')->name('login');
    $this->post('/login','Auth/LoginController@login');
    $this->get('/register','Auth/RegisterController@show')->name('register');
    $this->post('/register','Auth/RegisterController@register');
});
