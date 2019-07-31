<?php namespace System\Libraries;

use FilesystemIterator;
use RuntimeException;
use const LOCK_EX;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Files
 */
class File
{
    public function create($path): bool
    {
        return touch($path);
    }


    public function open(String $fileAndMode, callable $callback = null)
    {
        if (strpos('|', $fileAndMode)) {
            list($file, $mode) = explode('|', $fileAndMode, 2);
        } else {
            list($file, $mode) = array($fileAndMode, 'r+');
        }


        if ($callback === null) {
            return fopen($file, $mode);
        }

        return $callback(fopen($file, $mode), $this);
    }


    public function close($file): bool
    {
        return is_resource($file) ? fclose($file) : false;
    }


    public function dirIsEmpty($dir): bool
    {
        $iterator = new FilesystemIterator($dir);

        return !$iterator->valid();
    }


    public function size($path)
    {
        return filesize($path);
    }


    public function lastModifiedTime($path)
    {
        return filemtime($path);
    }


    public function isImage($file): Bool
    {
        if (isset($file['tmp_name'])) {
            return @getimagesize($file['tmp_name']) ? true : false;
        }

        return @getimagesize($file) ? true : false;
    }


    public function setDir($pathname, $mode = 0755, $recursive = false): Bool
    {
        return mkdir($pathname, $mode, $recursive);
    }

    public function deleteDirectory($pathname): Bool
    {
        if (!$this->isDir($pathname)) {
            return false;
        }
        return rmdir($pathname);
    }

    public function isDir($pathname): Bool
    {
        return is_dir($pathname);
    }

    /**
     * @param string $directory
     *
     */

    public function rmdir_r(String $directory)
    {
        if (is_dir($directory)) {
            $this->flashDir($directory);

            rmdir($directory);
        }
    }


    public function flashDir($directory)
    {
        foreach (glob($directory . '/*') as $file) {
            if (is_dir($file)) {
                $this->flashDir($file);
            } else {
                unlink($file);
            }
        }
    }


    public function import($file, $once = true)
    {
        if ($this->exists($file)) {
            if ($once) {
                return  (require_once $file);
            }

            return (require $file);
        }

        throw new \RuntimeException("File not found.Path: ({$file})");
    }

    public function exists($filename): Bool
    {
        return file_exists($filename);
    }

    public function importOnce($file)
    {
        if ($this->exists($file)) {
            return (require_once $file);
        }

        throw new \RuntimeException("File not found.Path: ({$file})");
    }

    public function write($path, $content, $lock = false)
    {
        if (is_resource($path)) {
            $return = fwrite($path, $content);

            if ($lock) {
                flock($path);
            }

            fclose($path);

            return $return;
        }

        return file_put_contents($path, $content, $lock ? LOCK_EX : 0);
    }

    public function is($path): Bool
    {
        return is_file($path);
    }

    public function append($file, $data)
    {
        return file_put_contents($file, $data, FILE_APPEND);
    }

    public function prepend(String $file, $content)
    {
        return file_put_contents($file, $content . $this->get($file));
    }

    public function get($path)
    {
        if ($this->isFile($path)) {
            return file_get_contents($path);
        }
        throw new \RuntimeException('File does not exist at path ' . $path);
    }

    public function chmod($path, $mode = null)
    {
        if ($mode !== null) {
            return chmod($path, $mode);
        }
        return substr(fileperms($path), -4);
    }


    public function delete($files): Bool
    {
        $_files = is_array($files) ? $files : func_num_args();

        $error = 0;

        foreach ($_files as $file) {
            if (!@unlink($file)) {
                $error++;
            }
        }
        return !($error > 0);
    }


    public function move($path, $target): Bool
    {
        return rename($path, $target);
    }


    public function copy($path, $target): Bool
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


    public function getName($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }


    public function getExtension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function basename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }
}
