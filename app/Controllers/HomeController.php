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

//use System\Engine\Http\Request;
use System\Facades\Language;
use System\Facades\Redirect;
use System\Libraries\View\View;

class HomeController extends Controller
{



    /**
     * HomeController welcome method.Show Home page
     *
     * @return View
     */
    public function welcome(): View
    {
        return view('welcome');
    }

    /**
     * @return View
     */
    public function home(): View
    {
        return view('home');
    }


    /**
     * HomeController changeLanguage method.Change site content language
     *
     * @param $lang
     * @return \System\Libraries\Redirect
     */
    public function language($lang): \System\Libraries\Redirect
    {
        if (in_array($lang, array('az','en','tr'))) {
            Language::locale($lang);
        }

        return Redirect::back();
    }
}
