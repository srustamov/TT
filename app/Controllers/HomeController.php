<?php namespace App\Controllers;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



//use App\Controllers\Controller;
//use System\Engine\Http\Request;
use System\Facades\Language;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }



    public function changeLanguage($lang)
    {

        if (in_array($lang, ['az','en','tr'])) {
            Language::set($lang);
        }

        return redirect()->back();
    }
}
