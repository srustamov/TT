<?php namespace System\Libraries;

/**
 * @package  TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage Library
 * @category  Input
 */


class Input
{

    private $data;


    public function get($name = false)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            if (!$name) return $_GET;

            if (isset($_GET[ $name ]))
            {
                if (is_array($_GET[ $name ]))
                {
                    return array_map(function ($item) {
                        return $this->filter($item);
                    }, $_GET[ $name ]);
                }
                return $this->filter($_GET[ $name ]);
            }
        }
        return false;
    }


    public function post($name = false)
    {
        if ($_SERVER[ 'REQUEST_METHOD' ] == 'POST')
        {
            if (!$name) return $_POST;

            if (isset($_POST[ $name ]))
            {

              if (is_array($_POST[ $name ]))
              {
                  return array_map(function ($item){
                      return $this->filter($item);
                  }, $_POST[ $name ]);
              }

              return $this->filter($_POST[ $name ]);

            }
        }
        return false;
    }


    public function file($name)
    {
      if(isset($_FILES[$name]))
      {
        if($_FILES[$name]['error'] > 0)
        {
          return false;
        }
        return $_FILES[$name];
      }
      return false;
    }


    public function all()
    {
        if(is_null($this->data)) {
          parse_str(file_get_contents('php://input'),$input_vars);
        } else {
          $input_vars = $this->data;
        }

        return $input_vars;
    }


    public function xssClean($data)
    {
        if (is_array($data))
        {
            foreach ($data as $key => $value)
            {
                $data[$key] = $this->xssClean($value);
            }
            return $data;
        }


        $data = str_replace(array( '&amp;' , '&lt;' , '&gt;' ), array( '&amp;amp;' , '&amp;lt;' , '&amp;gt;' ), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        return $data;
    }


    public function filter(String $str):String
    {
        $str = html_entity_decode($str, ENT_QUOTES);
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8', false);
    }


    public function __call($method,$args)
    {
      if(in_array(strtoupper($method),['PUT' , 'DELETE' , 'PATCH'])) {
        $data = $this->all();
        if(isset($args[0]) && !is_array($args[0])) {
          return $data[$args[0]] ?? false;
        } else {
          return $data;
        }
      } else {
        throw new \BadMethodCallException("Call to undefined method Input::$method()");
      }
    }


    public function __get($key)
    {
      $data = $this->all();

      if (isset($data[$key])) {
        return !is_array($data[$key]) ? trim($data[$key]) : $data[$key];
      }

      return false;
    }


}
