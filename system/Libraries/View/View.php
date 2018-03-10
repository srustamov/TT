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



class View
{


    protected $file;

    protected $data = [];

    protected $content;

    protected $minify;


    public function render ( String $file , $data = [] , $content = false )
    {
        $this->file = $file;
        $this->data = array_merge ( $this->data , $data );
        $this->content = $content;

        return $this;
    }


    public function data ( $key , $value = null )
    {
        if (is_array ( $key )) {
            $this->data = array_merge ( $this->data , $key );
        } else {
            $this->data[ $key ] = $value;
        }

        return $this;
    }


    public function minify(Bool $minify = true)
    {
        $this->minify = $minify;
        return $this;
    }


    public function getContent ()
    {
        $this->content = true;
    }


    private function withFlashData()
    {
        if (app ( 'session' )->has ( md5 ( 'flash-data' ) )) {
            $flash_name = app ( 'session' )->get ( md5 ( 'flash-name' ) );
            $flash_data = app ( 'session' )->get ( md5 ( 'flash-data' ) );
            if (!isset( $this->data[ $flash_name ] )) {
                $this->data[ $flash_name ] = new Errors($flash_data);
                app ( 'session' )->delete ( [
                        md5 ( 'flash-data' ) ,
                        md5 ( 'flash-name' )
                    ]
                );
            } else {
                if(!isset($this->data['errors'])) {
                    $this->data['errors'] = new Errors;
                }
            }
        } else {
            if(!isset($this->data['errors'])) {
                $this->data['errors'] = new Errors;
            }
        }
    }


    private function reset()
    {
        $this->file = null;
        $this->data = [];
        $this->content = null;
        $this->minify  = null;
    }


    public function __destruct ()
    {

        $this->withFlashData();

        $loader = new EdgeFileLoader( array( path ( 'app/Views' ) ) );

        $loader->addFileExtension ( '.php' );

        $edge = new Edge( $loader , null , new EdgeFileCache( config ( 'view.cache_path' ) ) );

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

        if ($this->content === true)
        {
            $this->reset();
            return $content;
        }
        else
        {
            $this->reset();
            echo $content;
        }

    }


}
