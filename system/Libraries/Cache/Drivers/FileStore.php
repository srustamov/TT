<?php namespace System\Libraries\Cache\Drivers;


use System\Facades\Load;


class FileStore implements CacheStore
{


    private $path;

    private $fullpath;

    private $put = false;

    private $key;

    private $expires;


    function __construct ()
    {
        $this->path = Load::config('cache.file',['path' => path('storage/cache/data')])['path'];

        $this->gc();
    }


    public function put(String $key , $value ,$expires = null)
    {

        $paths   = $this->getpaths($key);

        $this->fullpath = $paths->fullpath;

        if(!$this->has($key))
        {
            $this->createDir($paths);
        }

        if(is_callable($value))
        {
            $value = call_user_func($value,$this);
        }


        if(is_null($expires))
        {

          if(is_null($this->expires))
          {
            $this->put = true;

            $this->key = $key;

            file_put_contents($paths->fullpath,serialize($value));
          }
          else
          {

            file_put_contents($paths->fullpath,serialize($value));

            touch($paths->fullpath ,time() + $this->expires);


            $this->expires = null;
          }

        }
        else
        {

          file_put_contents($paths->fullpath,serialize($value));

          touch($paths->fullpath ,time() + $expires);

          $this->expires = null;
        }

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
        if(is_callable($key))
        {
            $key = call_user_func($key,$this);
        }

        $paths = $this->getpaths($key);

        if (file_exists($paths->fullpath))
        {
          unlink($paths->fullpath);
        }

        if (Load::class('file')->dirIsEmpty($this->path.'/'.$paths->path1.'/'.$paths->path2))
        {
            rmdir($this->path.'/'.$paths->path1.'/'.$paths->path2);

            if (Load::class('file')->dirIsEmpty($this->path.'/'.$paths->path1))
            {
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


    public function expires ( Int $expires )
    {
        if($this->put && !is_null($this->key))
        {

            touch($this->fullpath,time() + $expires);

            $this->put = false;

            $this->key = null;
        }
        else
        {
            $this->expires = $expires;
        }

        return $this;
    }


    public function minutes ( Int $minutes )
    {
      return $this->expires($minutes * 60);
    }


    public function hours ( Int $hours )
    {
      return $this->expires($hours * 3600);
    }


    public function day ( Int $day )
    {
      return $this->expires($day * 3600 * 24);
    }



    private function existsExpires($paths)
    {
        if(file_exists($paths->fullpath))
        {
            if(filemtime($paths->fullpath) <= time())
            {
                unlink($paths->fullpath);

                if (Load::class('file')->dirIsEmpty($this->path.'/'.$paths->path1.'/'.$paths->path2))
                {
                    rmdir($this->path.'/'.$paths->path1.'/'.$paths->path2);

                    if (Load::class('file')->dirIsEmpty($this->path.'/'.$paths->path1))
                    {
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
      $this->flushDir($this->path);
    }



    private function flushDir($dir)
    {
      foreach (glob($dir.'/*') as $file)
      {
        if(is_dir($file))
        {
          $this->flushDir($file);

          rmdir($file);
        }
        else
        {
          unlink($file);
        }
      }
    }


    public function gc()
    {
      $directoryRead = glob($this->path."/*");

      foreach ($directoryRead as $file)
      {
        if(is_file($file) && filemtime($file) < time())
        {
          unlink($file);
        }
      }

      foreach ($directoryRead as $dir)
      {
        if(is_dir($dir) && Load::class('file')->dirIsEmpty($dir))
        {
          rmdir($dir);
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



    public function close ()
    {
        return true;
    }



}
