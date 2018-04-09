<?php namespace System\Engine\Http;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */



class UploadedFile extends \SplFileInfo
{


    private $name;

    private $size;

    private $error;

    private $uploadError;

    private $mimeType;


    function __construct(array $files)
    {

        $this->error = $files['error'] ? : UPLOAD_ERR_OK;

        $this->setName($files['name']);

        $this->size = $files['size'];

        $this->mimeType = $files['type'];

        parent::__construct($files['tmp_name']);

    }


    public function isValid()
    {
        $isOk = $this->error === UPLOAD_ERR_OK;

        return $isOk && is_uploaded_file($this->getPathname());
    }


    public function setName($name)
    {
        $originalName = str_replace('\\', '/', $name);
        $position     = strrpos($originalName, '/');
        $originalName = false === $position ? $originalName : substr($originalName, $position + 1);

        $this->name =  $originalName;
    }


    public function size()
    {
        return $this->size;
    }

    public function name()
    {
        return $this->name;
    }


    public function extension()
    {
        return $this->getExtension();
    }


    public function mimeType()
    {
        return $this->mimeType;
    }

    public function uploadErrorMessage()
    {
        return $this->uploadError;
    }


    public function move($target,String $name = null)
    {
        if($this->isValid())
        {
            $target = rtrim($target,'/').'/';

            $name   = !is_null($name) ? $name : $this->name();

            if(!is_dir($target))
            {
                @mkdir($target,0777,true);
            }

            if(!@move_uploaded_file($this->getRealPath(),$target.$name))
            {
                $error = error_get_last();

                $this->uploadError = $error['message'] ?? '';

                return false;
            }

            return true;
        }

        return false;
    }



}
