<?php namespace App\Controllers\Backend;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */


use App\Controllers\Controller;



class AdminController extends Controller
{

    function __construct()
    {
      $this->middleware('auth:admin');
    }


    public function dashboard ()
    {
        // Dashboard
    }




}
