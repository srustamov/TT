<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Libraries
 * @category    Files
 */

class File
{
    public function size($path)
    {
        return filesize($path);
    }



    public function lastModifiedTime($path)
    {
        return filemtime($path);
    }


    public function isImage($file):Bool
    {
      if (isset($file['tmp_name']))
      {
          return @getimagesize($file['tmp_name']) ? true :  false;
      }
    }


    public function setDir($pathname, $mode = 0755, $recursive = false):Bool
    {
        return @mkdir($pathname, $mode, $recursive);
    }



    public function isDir($pathname):Bool
    {
        return is_dir($pathname);
    }



    public function deleteDirectory($pathname):Bool
    {
        if (!$this->isDir($pathname)) {
            return false;
        }
        return @rmdir($pathname);
    }




    public function require($file)
    {
        if ($this->exists($file)) {
            return require $file;
        }
        throw new \Exception("File not found.Path: ({$file})", 1001);
    }



    public function requireOnce($file)
    {
        if ($this->exists($file)) {
            return require_once $file;
        }
        throw new \Exception("File not found.Path: ({$file})", 1001);
    }


    public function exists($filename):Bool
    {
        return file_exists($filename);
    }


    public function write($path, $contents, $lock = false)
    {
        $lock = $lock ? LOCK_EX : 0;
        return file_put_contents($path, $contents, $lock);
    }


    public function get($path)
    {
        if ($this->isFile($path)) {
            return file_get_contents($path);
        }
        throw new \Exception("File does not exist at path ".$path);
    }



    public function isFile($path):Bool
    {
        return is_file($path);
    }


    public function append($file, $data)
    {
        return file_put_contents($file, $data, FILE_APPEND);
    }


    public function chmod($path, $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }
        return substr(fileperms($path), -4);
    }


    public function delete($files):Bool
    {
        $files = is_array($files) ? $files : func_num_args();
        $error = 0;
        foreach ($files as $file) {
            try {
                if (! @unlink($file)) {
                    $error++;
                }
            } catch (\Exception $e) {
                $error++;
            }
        }
        return !($error > 0);
    }


    public function move($path, $target):Bool
    {
        return rename($path, $target);
    }


    public function copy($path, $target):Bool
    {
        return copy($path, $target);
    }


    public function type($path)
    {
        return filetype($path);
    }



    public function mimeType($path)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }
}
