<?php namespace App\Controllers;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */



use App\Controllers\Controller;
use System\Engine\Http\Request;
use System\Facades\Language;



class HomeController extends Controller
{



    public function index ()
    {
      return view('home');
    }



    public function changeLanguage($language)
    {
      if(in_array($language,['az','en','tr']))
      {
        Language::set($language);
      }

      return redirect()->back();
    }




}
