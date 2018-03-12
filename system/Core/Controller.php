<?php namespace System\Core;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */
//-------------------------------------------------------------


//-------------------------------------------------------------
// Controller Class
//-------------------------------------------------------------

use System\Libraries\View\View;
use System\Engine\Http\Middleware;

abstract class Controller
{


//-------------------------------------------------------------
    private static $instance;
//-------------------------------------------------------------


//-------------------------------------------------------------
//    Controller Constructor
//-------------------------------------------------------------


    function __construct()
    {
        self::$instance =& $this;
    }





//-------------------------------------------------------------
//    Controller view
//-------------------------------------------------------------

    /**
     * @param String $file
     * @param array|bool $data
     * @param bool $content
     * @return \System\Libraries\View\View
     */

    public function view(String $file, array $data = [], $content = false)
    {
        return (new View)->render($file, $data, $content);
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
//    Class Load
//-------------------------------------------------------------


    /**
     * @param $class
     * @param $namespace
     * @throws \Exception
     */
    public function loadClass( $class, $namespace)
    {
        $alias = $class;

        if(strpos(':',$class) !== false)
        {
          list($class,$alias) = explode(':',$class,2);
        }

        if (class_exists("\\{$namespace}\\{$class}"))
        {
            $key = 'Class'.md5(uniqid());
            class_alias("\\$namespace\\$class", "$key");
            $this->{$alias} = new $key;
        }
        else
        {
            throw new \Exception("Class {$namespace}\\{$class}  not found");
        }
    }
}
