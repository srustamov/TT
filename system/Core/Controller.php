<?php namespace System\Core;
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


//-------------------------------------------------------------
// Base Controller Class
//-------------------------------------------------------------

use System\Facades\View;
use System\Engine\Http\Middleware;

abstract class Controller
{




    protected function view(String $file, array $data = [])
    {
        return View::render($file, $data, $content);
    }




    protected function middleware()
    {
      if(func_num_args()  > 0)
      {
        $args = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

        foreach ($args as $extension)
        {
          Middleware::init($extension);
        }
      }
    }



    protected function callAction(String $action,Array $args = [],$namespace = 'App\\Controllers')
    {
      if(strpos($action,'@') !== false)
      {
        list($controller,$method) = explode('@',$action);

        $controller = '\\'.$namespace.'\\'.str_replace('/','\\',$controller);

        return call_user_func_array([new $controller,$method], $args);

      }
      else
      {

        list($controller,$method) = array($this,$action);

        return call_user_func_array([$this,$method], $args);

      }


    }

}
