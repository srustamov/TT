<?php namespace System\Libraries\Cache\Drivers;

use System\Libraries\Cache\CacheStore;

class FileStore implements CacheStore
{


    private $path;


    private $fullpath;


    private $put;


    private $expires;


    function __construct ()
    {
        $this->path = config('cache.file',['path' => path('storage/cache/data')])['path'];
    }


    public function put(String $key , $value ,$expires = null)
    {

        $this->expires = $expires;

        $this->put     = true;

        $paths   = $this->getpaths($key);

        $this->fullpath = $paths->fullpath;

        if(!$this->has($key)) {
            $this->createDir($paths);
        }

        if(is_callable($value)) {
            $value = call_user_func($value,$this);
        }
        file_put_contents($paths->fullpath,serialize($value));

        return $this;
    }



    public function forever(String $key , $value )
    {
        $this->put($key , $value , time());
        return $this;
    }




    public function has($key)
    {
        if(is_callable($key))
        {
            $key = call_user_func($key,$this);
        }
        return $this->existsExpires($this->getpaths($key));
    }



    public function get($key)
    {
        if(is_callable($key))
        {
            $key = call_user_func($key,$this);
        }

        $paths = $this->getpaths($key);

        if($this->existsExpires($paths))
        {
            return unserialize(file_get_contents($paths->fullpath));
        }
        return false;
    }



    public function forget($key)
    {
        if(is_callable($key)) {
            $key = call_user_func($key,$this);
        }

        $paths = $this->getpaths($key);

        if (file_exists($paths->fullpath)) {
          unlink($paths->fullpath);
        }

        if (app('file')->is_dir_empty($this->path.'/'.$paths->path1.'/'.$paths->path2)) {
            rmdir($this->path.'/'.$paths->path1.'/'.$paths->path2);
            if (app('file')->is_dir_empty($this->path.'/'.$paths->path1)) {
              rmdir($this->path.'/'.$paths->path1);
            }
        }

    }


    private function createDir($paths)
    {

        if(!file_exists($paths->fullpath))
        {
            if(!file_exists($this->path.'/'.$paths->path1.'/'))
            {
                mkdir($this->path.'/'.$paths->path1.'/',0755,false);
            }
            mkdir($this->path.'/'.$paths->path1.'/'.$paths->path2.'/',0755,false);
        }

        return $paths->fullpath;

    }


    public function expires(Int $expires)
    {
        $this->expires = $expires;

        return $this;
    }


    public function minutes(Int $minutes)
    {
        $this->expires = $minutes * 60;

        return $this;
    }


    private function existsExpires($paths)
    {
        if(file_exists($paths->fullpath))
        {
            if(filemtime($paths->fullpath) <= time())
            {
                unlink($paths->fullpath);
                if (app('file')->is_dir_empty($this->path.'/'.$paths->path1.'/'.$paths->path2)) {
                    rmdir($this->path.'/'.$paths->path1.'/'.$paths->path2);
                    if (app('file')->is_dir_empty($this->path.'/'.$paths->path1)) {
                      rmdir($this->path.'/'.$paths->path1);
                    }
                }
                return false;
            }
            return true;
        }
        return false;
    }


    private function getpaths($key)
    {
        $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);

        $fullpath = $this->path.'/'.$parts[0].'/'.$parts[1].'/'.$hash;

        return (object) array('path1' => $parts[0],'path2' => $parts[1], 'fullpath' => $fullpath);

    }


    public function flush()
    {
      $this->flushDir(path('/storage/cache/data'));
    }



    private function flushDir($dir)
    {
      foreach (glob($dir.'/*') as $file) {
        if(is_dir($file)) {
          $this->flushDir($file);
        } else {
          unlink($file);
        }
      }
    }



    public function __get($key)
    {
        return $this->get($key);
    }



    public function __call($method,$args)
    {
      throw new CacheFileStoreException("Call to undefined method Cache::$method()");
    }




    public function __destruct()
    {
        if(!is_null($this->put) && !is_null($this->expires))
        {
            touch($this->fullpath , time()+ $this->expires);
        }
    }

}
