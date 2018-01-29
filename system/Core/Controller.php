<?php namespace System\Core;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */
//-------------------------------------------------------------


//-------------------------------------------------------------
// Controller Class
//-------------------------------------------------------------

use System\Libraries\Request;
use System\Libraries\View;
use System\Engine\Http\Middleware;

class Controller
{


//-------------------------------------------------------------
    private static $instance;
//-------------------------------------------------------------


//-------------------------------------------------------------
//    Controller Constructor
//-------------------------------------------------------------


    public function __construct()
    {
        self::$instance =& $this;
    }





//-------------------------------------------------------------
//    Controller view
//-------------------------------------------------------------

    /**
     * @param String $file
     * @param array|bool $data
     * @param bool $cache
     */

    public function view(String $file, array $data = [], $cache = false)
    {
        return (new View())->render($file, $data, $cache);
    }



//-------------------------------------------------------------
//    Middleware
//-------------------------------------------------------------

    public function middleware()
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



//-------------------------------------------------------------
//    Model Load
//-------------------------------------------------------------


    public function model($model, $namespace = "App\\Models")
    {
        $alias = $model;
        if(strpos(':',$model) !== false)
        {
          list($model,$alias) = explode(':', $model,2);
        }
        if (class_exists("\\{$namespace}\\{$model}"))
        {
            $key = 'Model'.md5(uniqid());
            class_alias("\\{$namespace}\\{$model}", "{$key}");
            $this->{$alias} = new $key;
        }
        else
        {
            throw new \Exception("Model {$namespace}\\{$model} class not found");
        }
    }



//-------------------------------------------------------------
//    Library Load
//-------------------------------------------------------------



    public function library($library, $namespace = 'System\\Libraries')
    {
        $alias = $library;
        if(strpos(':',$library) !== false)
        {
          list($library,$alias) = explode(':',$library,2);
        }

        if (class_exists("\\{$namespace}\\{$library}"))
        {
            $key = 'Library'.md5(uniqid());
            class_alias("\\$namespace\\$library", "$key");
            $this->{$alias} = new $key;
        }
        else
        {
            throw new \Exception("Library {$namespace}\\{$library} class not found");
        }
    }
}
