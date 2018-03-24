<?php

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Storage
 */

namespace System\Libraries;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;

class Storage
{

    protected $path = 'storage/public';


    public function put ( String $file , $content )
    {
        if(is_array($content) && isset($content['tmp_name']) ) {
          return move_uploaded_file($content['tmp_name'], $this->fixPath ( $file ) );
        } elseif (is_string($content)) {
          return file_put_contents ( $this->fixPath ( $file ) , $content );
        } else {
          throw new Exception("File write content type wrong!");
        }
    }

    public function prepend ( String $file , $content )
    {
        return file_put_contents (
          $this->fixPath ( $file ) , $content . $this->get ( $file )
        );
    }

    public function get ( $file ,Callable $callback = null)
    {
        if ($this->exists ( $file )) {
            if(!is_null($callback)) {
              return call_user_func($callback,file_get_contents ( $this->fixPath ( $file ) ));
            } else {
              return file_get_contents ( $this->fixPath ( $file ) );
            }
        }
        return false;
    }

    public function exists ( $file )
    {
        return file_exists ( $this->fixPath ( $file ) );
    }

    public function append ( String $file , $content )
    {
        return file_put_contents ( $this->fixPath ( $file ) , $content , FILE_APPEND );
    }

    public function directories ( $path )
    {
        $directories = [];

        $storagePrefix = $this->fixPath ();

        foreach (glob ( rtrim ( $this->fixPath ( $path ) , '/' ) . "/*" ) as $item) {
            if (is_dir ( $item )) {
                $fullPathParts = explode ( $storagePrefix , $item , 2 );

                $directories[] = array_pop ( $fullPathParts );
            }
        }

        return $directories;
    }

    public function allDirectories ( $path )
    {

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $this->fixPath ( $path ) )
        );

        $directories = [];

        $storagePrefix = $this->fixPath ();

        foreach ($iterator as $dir) {
            if ($dir->isDir ()) {
                $nameParts = explode ( $storagePrefix , $dir->getPathName () );

                $value = rtrim ( array_pop ( $nameParts ) , '.' );

                if (array_search ( $value , $directories ) === false) {
                    $directories[] = $value;
                }

            }
        }

        return array_values ( array_filter ( $directories ) );
    }

    public function allFiles ( $path )
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $this->fixPath ( $path ) )
        );

        $files = [];

        $storagePrefix = $this->fixPath ();

        foreach ($iterator as $file) {
            if (!$file->isDir ()) {
                $fullPathParts = explode ( $storagePrefix , $file->getPathname () , 2 );

                $files[] = array_pop ( $fullPathParts );
            }
        }

        return $files;
    }

    public function files ( $path )
    {
        $files = [];

        $storagePrefix = $this->fixPath ();

        foreach (glob ( rtrim ( $this->fixPath ( $path ) , '/' ) . "/*" ) as $item) {
            if (is_file ( $item )) {
                $fullPathParts = explode ( $storagePrefix , $item , 2 );

                $files[] = array_pop ( $fullPathParts );
            }
        }

        return $files;
    }

    public function delete ( $files )
    {
        $files = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

        foreach ($files as $file) {
            unlink ( $this->fixPath ( $file ) );
        }
    }

    public function rmdir ( $directories )
    {
        $directories = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

        foreach ($directories as $directory) {
            rmdir ( $this->fixPath ( $directory ) );
        }
    }

    public function size ( $file )
    {
        return filesize ( $this->fixPath ( $file ) );
    }

    public function copy ( $source , $copy )
    {
        return copy ( $this->fixPath ( $source ) , $this->fixPath ( $copy ) );
    }

    public function move ( $source , $copy )
    {
        return rename ( $this->fixPath ( $source ) , $this->fixPath ( $copy ) );
    }

    public function mkdir ( $dir , $mode = 0777 )
    {
        return mkdir ( $this->fixPath ( $dir ) , 0777 );
    }

    public function touch ( $file )
    {
        return touch ( $this->fixPath ( $file ) );
    }

    public function modifiedTime ( $file )
    {
        return @filemtime ( $this->fixPath ( $file ) );
    }

    public function path ( $path )
    {
        $this->path = $path;

        return $this;
    }

    protected function fixPath ( $path = '' )
    {
        return path ( trim($this->path,'/'). '/' . trim ( $path , '/' ) );
    }

}
