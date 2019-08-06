<?php namespace System\Libraries;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage  Library
 * @category   Redirect
 */



use System\Engine\App;
use System\Facades\Route;

class Redirect
{
    public function __construct()
    {
        if (func_num_args() > 0) {
            call_user_func_array([$this,'to'], func_get_args());
        }
    }


    public function route($name, array $parameters = [])
    {
        return $this->to(Route::getName($name, $parameters));
    }


    public function back($refresh = 0, $http_response_code = 302)
    {
        if ($refresh) {
            $this->refresh = $refresh;
        }

        if ($back = App::get('http')->referer()) {
            $url = $back;
        } elseif (($back = App::get('session')->prevUrl())) {
            $url = $back;
        } else {
            $url = '';
        }

        return call_user_func_array([$this,'to'], array($url,$refresh,$http_response_code));
    }


    public function to(String $url, $refresh = 0, $http_response_code = 302): self
    {
        $url = $this->prepareUrl($url);

        App::get('response')->redirect($url,$refresh,$http_response_code);

        return $this;
    }



    public function with($key, $value = null): self
    {
        $data = is_array($key) ?:[$key => $value];

        foreach ($data as $key => $value) {
            App::get('session')->flash($key, $value);
        }

        return $this;
    }



    public function withErrors($key, $value = null): self
    {
        $data = is_array($key) ? $key : [$key => $value];

        App::get('session')->flash('view-errors', $data);

        return $this;
    }


    protected function prepareUrl($url)
    {
        if (empty(trim($url))) {
            throw new \Exception('Redirect location empty url');
        }


        if (!preg_match('/^https?:\/\//', $url)) {
            $url = App::get('url')->to($url);
        }

        return $url;
    }


    public function __call($method, $args)
    {
        if (func_num_args() > 0) {
            if (strlen($method) > 4 && substr($method, 0, 4) == 'with') {
                $method = strtolower($method);

                $var  = substr($method, 4);

                $args = is_array($args[0]) ? $args[0] : [$args[0] => $args[1] ?? null];

                App::get('session')->flash($var, $args[0]);
            } else {
                throw new \BadMethodCallException("Call to undefined method Redirect::{$method}()");
            }
        }
        return $this;
    }

    public function instance()
    {
        return $this;
    }

    public function __toString(){}

}
