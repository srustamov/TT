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



    public function get($name = false, $xssClean = false)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            if (!$name) return $_GET;

            if (isset($_GET[ $name ]))
            {
                if ($xssClean == false)
                {
                    if (is_array($_GET[ $name ]))
                    {
                        return array_map(function ($item) {
                            return Http::filter($item);
                        }, $_GET[ $name ]);
                    }
                    return $this->filter($_GET[ $name ]);
                }
                else
                {
                    if (is_array($_GET[ $name ]))
                    {
                        return array_map(function ($item) {
                            return $this->filter($this->xssClean($item));
                        }, $_GET[ $name ]);
                    }
                    return $this->filter($this->xssClean($_GET[ $name ]));
                }
            }
            return false;
        }
        return false;
    }



    public function xssClean($data, $image = false)
    {
        if (is_array($data))
        {
            foreach ($data as $key => $value)
            {
                $data[$key] = $this->xssClean($value);
            }
            return $data;
        }

        if ($image) return $data;

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


    public function post($name = false, $xssClean = false)
    {
        if ($_SERVER[ 'REQUEST_METHOD' ] == 'POST')
        {
            if (!$name) return $_POST;

            if (isset($_POST[ $name ]))
            {
                if ($xssClean == false)
                {
                    if (is_array($_POST[ $name ]))
                    {
                        return array_map(function ($item){
                            return $this->filter($item);
                        }, $_POST[ $name ]);
                    }

                    return $this->filter($_POST[ $name ]);
                }
                else
                {
                    if (is_array($_POST[$name]))
                    {
                        return array_map(function ($item){
                            return $this->filter($this->xssClean($item));
                        }, $_POST[$name]);
                    }
                    return $this->filter($this->xssClean($_POST[ $name ]));
                }
            }
            return false;
        }
        return false;
    }



    public function put($name = false, $xssClean = false)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT')
        {
            parse_str(file_get_contents("php://input"), $data);

            if (!$name) return $data;

            if (isset($data[ $name ]))
            {
                if ($xssClean == false)
                {
                    if (is_array($data[ $name ]))
                    {
                        return array_map(function ($item)
                        {
                            return $this->filter($item);
                        }, $data[ $name ]);
                    }
                    return $this->filter($data[ $name ]);
                }
                else
                {
                    if (is_array($data[ $name ]))
                    {
                        return array_map(function ($item) {
                            return $this->filter($this->xssClean($item));
                        }, $data[ $name ]);
                    }

                    return $this->filter($this->xssClean($data[ $name ]));
                }
            }
            return false;
        }
        return false;
    }



    public function delete($name = false, $xssClean = false)
    {
        if ($_SERVER[ 'REQUEST_METHOD' ] == 'DELETE')
        {
            parse_str(file_get_contents("php://input"), $data);

            if (!$name) return $data;

            if (isset($data[ $name ])) {
                if ($xssClean == false)
                {
                    if (is_array($data[ $name ]))
                    {
                        return array_map(function ($item) {
                            return $this->filter($item);
                        }, $data[ $name ]);
                    }
                    return $this->filter($data[ $name ]);
                }
                else
                {
                    if (is_array($data[ $name ]))
                    {
                        return array_map(function ($item) {
                            return $this->filter($this->xssClean($item));
                        }, $data[ $name ]);
                    }

                    return $this->filter($this->xssClean($data[ $name ]));
                }
            }
            return false;
        }
        return false;
    }



    public function request($name = false, $xssClean = false)
    {
        if (!$name) return $_REQUEST;

        if (isset($_REQUEST[$name]))
        {
            if ($xssClean == false)
            {
                if (is_array($_REQUEST[ $name ]))
                {
                    return array_map(function ($item) {
                        return $this->filter($item);
                    }, $_REQUEST[ $name ]);
                }

                return $this->filter($_REQUEST[ $name ]);
            }
            else
            {
                if (is_array($_REQUEST[ $name ]))
                {
                    return array_map(function ($item) {
                        return $this->filter($this->xssClean($item));
                    }, $_REQUEST[ $name ]);
                }

                return $this->filter($this->xssClean($_REQUEST[ $name ]));
            }
        }
        return false;
    }


    /**
     * @param $name
     * @return array|bool
     */
    public function file($name = false)
    {
      if(isset($_FILES[$name]))
      {
        if($_FILES[$name]['error'] > 0)
        {
          return false;
        }
        else
        {
          return $_FILES[$name];
        }
      }
      else
      {
        return false;
      }
    }


    public function filter(String $str):String
    {
        $str = html_entity_decode($str, ENT_QUOTES);
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8', false);
    }
}
