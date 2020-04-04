<?php  namespace App\Controllers;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


use TT\Redirect;
use TT\Translation\Translator;
use App\Controllers\Controller;

class LanguageController extends Controller
{

    public function change($lang,Redirect $redirect,Translator $translator)
    {
        if (in_array($lang, array('az','en','tr'))) {
            $translator->locale($lang);
        }

        return $redirect->back();
    }

}
