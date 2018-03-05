<?php namespace System\Libraries;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage  Libraries
 * @category   View
 */



use Windwalker\Edge\Cache\EdgeFileCache;
use Windwalker\Edge\Edge;
use Windwalker\Edge\Loader\EdgeFileLoader;


class View
{



    protected $file;

    protected $data = [];

    protected $cache = true;

    private $getContent = false;


    public function render(String $file, $data = [], $cache = true)
    {
        $this->file  = $file;
        $this->data  = array_merge($this->data, $data);
        $this->cache = $cache;
        return $this;
    }


    public function data($key, $value = null)
    {
        if (is_array($key))
        {
            $this->data = array_merge($this->data, $key);
        }
        else
        {
            $this->data[ $key ] = $value;
        }

        return $this;
    }


    public function getContent()
    {
        $this->getContent = true;
    }





    public function __destruct()
    {
        if (app('session')->has(md5('redirectWithData')))
        {
            $redirect_variable_name = app('session')->get(md5('redirectWithVariableName'));
            $redirect_data          = app('session')->get(md5('redirectWithData'));
            if (!isset($this->data[ $redirect_variable_name ]))
            {
                $this->data[ $redirect_variable_name ] = $redirect_data;
                app('session')->delete([md5('redirectWithData'),md5('redirectWithVariableName')]);
            }
        }



        $loader = new EdgeFileLoader(array( APPDIR.'Views' ));

        $loader->addFileExtension('.php');

        if ($this->cache == false)
        {
            $edge = new Edge($loader);
        }
        else
        {
            $edge = new Edge($loader, null, new EdgeFileCache(config('view.cache_path')));
        }

        //$compiler = $edge->getCompiler();

        if($extension = config('view.extension'))
        {
          $extension = '\\'.trim($extension,'\\');

          $edge->addExtension(new $extension);
        }


        $content = $edge->render($this->file, $this->data);

        if($this->getContent == true)
        {
            return $content;
        }
        else
        {
            echo $content;
        }

    }




}
