<?php  namespace App\Controllers;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



class WelcomeController extends Controller
{


  public function index()
  {
    return view('welcome');
  }

}
