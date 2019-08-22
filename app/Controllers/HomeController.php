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


//use TT\Engine\Http\Request;
use TT\Libraries\Language;
use TT\Libraries\View\View;
use TT\Libraries\Redirect;

class HomeController extends Controller
{


    /**
     * HomeController welcome method.Show Home page
     *
     * @return View
     * @throws \Exception
     */
    public function welcome(): View
    {
        return view('welcome');
    }

    /**
     * @return View
     * @throws \Exception
     */
    public function home(): View
    {
        return view('home');
    }


    /**
     * Change site content language
     *
     * @param $lang
     * @param Redirect $redirect
     * @return Redirect
     */
    public function language($lang, Redirect $redirect,Language $language):Redirect
    {
        //$lang = request()->params('lang');

        if (in_array($lang, array('az','en','tr'))) {
            $language->locale($lang);
        }

        return $redirect->back();
    }
}
