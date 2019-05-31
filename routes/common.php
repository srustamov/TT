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



$this->get('/',function()
{
   return view('welcome');
});

/*
  // Bad

  $this->middleware('auth')->get('/home','HomeController@index')->name('home');

  //The Correct

  $this->get('/home','HomeController@index')->name('home')->middleware('auth');

*/

$this->get('/home','HomeController@index')->name('home');

$this->get('/language/{lang}','HomeController@language')->name('lang')->pattern('lang','[a-z]{2}');

$this->get(['path'=>'/auth/logout','middleware'=> array('auth'),'name'=>'logout'],'Auth/LoginController@logout');

$this->group(['prefix' => '/auth','middleware' => array('guest')],function()
{
    $this->get('/login','Auth/LoginController@show')->name('login');
    $this->post('/login','Auth/LoginController@login');
    $this->get('/register','Auth/RegisterController@show')->name('register');
    $this->post('/register','Auth/RegisterController@register');
});
