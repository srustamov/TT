<?php
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/srustamov/TT
 */

/*
  // Bad

  $this->middleware('auth')->get('/home','HomeController@index')->name('home');

  //The Correct

  $this->get('/home','HomeController@index')->name('home')->middleware('auth');

*/

$this->get('/', function(){
   return file_get_contents(
            app_path('Views/welcome.html')
          );
});

$this->get('/home/', 'HomeController@home')->name('home');

$this->get('/language/{lang}/', 'LanguageController@change')->name('lang')->pattern('lang', '[a-z]{2}');

$this->get(['path'=>'/auth/logout','middleware'=> ['auth'],'name'=>'logout'], 'Auth/LoginController@logout');

$this->group(['prefix' => '/auth','middleware' => ['guest']], function () {
    $this->get('/login/', 'Auth/LoginController@show')->name('login');
    $this->post('/login/', 'Auth/LoginController@login');
    $this->get('/register/', 'Auth/RegisterController@show')->name('register');
    $this->post('/register/', 'Auth/RegisterController@register');
});



/*
//sub domain example
$this->group(['domain' => 'admin.example.com'],function($domain)
{
    // admin.example.com
    $domain->get('/','Backend/DashbaordController@index');

});
*/

