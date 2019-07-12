<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


//-------------------------------------------------------------
// Base Controller Class
//-------------------------------------------------------------

use System\Facades\View;

abstract class Controller
{


    /**
     * @param String $file
     * @param array $data
     * @return \System\Libraries\View\View
     */
    protected function view(String $file, array $data = [])
    {
        return View::render($file, $data);
    }




    protected function middleware()
    {
        if (func_num_args()  > 0) {
            $args = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

            foreach ($args as $extension) {
                Middleware::init($extension);
            }
        }
    }



    protected function callAction(String $action, array $args = [], $namespace = 'App\\Controllers')
    {
        if (strpos($action, '@') !== false) {
            list($controller, $method) = explode('@', $action);

            $controller = '\\'.$namespace.'\\'.str_replace('/', '\\', $controller);

            return call_user_func_array([new $controller,$method], $args);
        } else {
            return call_user_func_array([$this,$action], $args);
        }
    }
}
