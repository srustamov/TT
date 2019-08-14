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

use System\Engine\Http\Request;
use System\Libraries\Language;
use System\Libraries\View\View;
use System\Libraries\Redirect;

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
