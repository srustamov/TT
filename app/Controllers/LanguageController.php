<?php  namespace App\Controllers;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


use TT\Libraries\Language;
use TT\Libraries\Redirect;


class LanguageController
{

    public function change($lang,Redirect $redirect,Language $language)
    {
        if (in_array($lang, array('az','en','tr'))) {
            $language->locale($lang);
        }

        return $redirect->back();
    }

}
