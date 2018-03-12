<?php namespace System\Libraries\View;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category   View
 */


use Windwalker\Edge\Cache\EdgeFileCache;
use Windwalker\Edge\Edge;
use Windwalker\Edge\Loader\EdgeFileLoader;
use Windwalker\Edge\Extension\EdgeExtensionInterface;
use System\Facades\Load;


class View
{


    protected $file;

    protected $data = [];

    protected $minify;


    public function render ( String $file , $data = [] )
    {
        $this->file = $file;
        $this->data = array_merge ( $this->data , $data );
        return $this;
    }


    public function file (String $file)
    {
      $this->file = $file;
      return $this;
    }


    public function data ( $key , $value = null )
    {
        if (is_array ( $key ))
        {
            $this->data = array_merge ( $this->data , $key );
        }
        else
        {
            $this->data[ $key ] = $value;
        }

        return $this;
    }


    public function minify(Bool $minify = true)
    {
        $this->minify = $minify;
        return $this;
    }


    protected function _withFlashData()
    {
        if (Load::class('session')->has ( 'view-errors' ))
        {
            $errors = new Errors(Load::class('session')->get('view-errors'));

            Load::class('session')->delete ( 'view-errors' );
        }

        if(!isset($this->data['errors'])) {
            $this->data['errors'] = $errors ?? new Errors;
        }
    }


    protected function reset()
    {
        $this->file    = null;
        $this->data    = [];
        $this->content = null;
        $this->minify  = null;
    }


    protected function finishRender()
    {

        if(is_null($this->file)) {
          throw new \Exception("View File not found");
        }

        $this->_withFlashData();

        $loader = new EdgeFileLoader( array( path ( 'app/Views' ) ) );


        foreach (Load::config ( 'view.file_extensions',[]) as $file_extension) {
          $loader->addFileExtension ( $file_extension );
        }

        $edge = new Edge( $loader , null , new EdgeFileCache( Load::config ( 'view.cache_path' ) ) );

        if ($extensions = config ( 'view.extensions')) {

            foreach ($extensions as $extension) {
                if (new $extension instanceof EdgeExtensionInterface) {
                    $edge->addExtension ( new $extension() );
                }

            }

        }

        $content = $edge->render ( $this->file , $this->data );

        if (is_null($this->minify)) {
            $this->minify = config('view.minify');
        }

        if ($this->minify) {
            $content  = preg_replace('/([\n]+)|([\s]{2})/','',$content);
        }

        $this->reset();

        return $content;

    }


    public function getContent()
    {
      return $this->finishRender();
    }


    public function __toString ()
    {
       return $this->finishRender();
    }


}
