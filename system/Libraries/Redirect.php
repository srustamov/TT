<?php namespace System\Libraries;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category   Redirect
 */



use System\Facades\Load;

class Redirect
{



    protected $link;

    protected $refresh;

    protected $http_response_code;





    public function __construct($link = null, $refresh = 0, $http_response_code = 302)
    {
        $this->link = $link;
        $this->refresh = $refresh;
        $this->http_response_code = $http_response_code;

        return $this;
    }




    public function back($refresh = 0)
    {
        if($refresh) {
            $this->refresh = $refresh;
        }

        if (($back = Load::class('http')->referer()))
        {
            $this->link = $back;
        }
        else
        {
            $this->link = 'javascript://history.go(-1)';
        }
        return $this;
    }





    public function to(String $link, $refresh = 0, $http_response_code = 302)
    {
        $this->link = $link;

        $this->refresh = $refresh;

        $this->http_response_code = $http_response_code;

        $this->refresh = $refresh;

        return $this;
    }


    public function refresh(Int $refresh)
    {
        $this->refresh = $refresh;
        return $this;
    }


    public function with($key,$value = null)
    {
        $data = is_array($key) ?:[$key => $value];

        foreach ($data as $key => $value)
        {
            Load::class('session')->flash($key,$value);
        }

        return $this;
    }



    public function __call($method, $args)
    {

        if(func_num_args() > 0)
        {
            if(strlen($method) > 4 && substr($method,0,4) == 'with')
            {
                $var = strtolower(substr($method,4));

                $args = is_array($args[0]) ? $args[0] : [$args[0] => $args[1] ?? null];

                if($var == 'errors')
                {
                    Load::class('session')->set('view-errors',$args);
                }
                else
                {
                    Load::class('session')->flash($var,$args[0]);
                }
            }
            else
            {
                throw new \BadMethodCallException("Call to undefined method Redirect::{$method}()");
            }
        }
        return $this;
    }


    protected function redirect()
    {
        if (is_null($this->link))
        {
            throw new \Exception('Redirect location not found');
        }


        if (!preg_match('/^https?:\/\/|^www./', $this->link))
        {
            $this->link = Load::class('url')->base($this->link);
        }


        if($this->refresh) {
            sleep($this->refresh);
        }

        header("Location:" . $this->link, true, $this->http_response_code);
        exit;

    }



    public function __toString ()
    {
       return $this->redirect();
    }


}
