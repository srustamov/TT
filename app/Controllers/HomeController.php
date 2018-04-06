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


use System\Facades\Language;
use System\Facades\Redirect;


class HomeController extends Controller
{



    /**
     * HomeController index method.Show Home page
     *
     * @return \System\Libraries\View\View
     */
    public function index()
    {
        return view('home');
    }



    /**
     * HomeController changeLanguage method.Change site content language
     *
     * @return \System\Libraries\Redirect
     */
    public function language($lang)
    {
        if (in_array($lang, array('az','en','tr')))
        {
            Language::locale($lang);
        }

        return Redirect::back();
    }
}
