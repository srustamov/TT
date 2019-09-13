<?php

/**
 * App\Controllers\HomeController file.
 *
 * @category Controller
 *
 * @package TT
 * @author  SamirRustamov <rustemovv96@gmail.com>
 * @link  https://github.com/srustamov/TT
 */

namespace App\Controllers;



class HomeController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }


    public function home()
    {
        return view('home');
    }

}
