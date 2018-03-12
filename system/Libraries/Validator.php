<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Libraries
 * @category    Validator
 */


use System\Facades\Language;
use System\Facades\DB;

class Validator
{

    protected $translator;



    protected $fields = [
        'email','integer','numeric',
        'ip','file','image'
    ];


    protected $messages  = [];


    public function __construct ()
    {
        $this->translator = Language::translate('validator');
    }


    public function make(array $data, array $rules,Array $messages = []):Validator
    {

        $this->messages = array_merge($this->messages,$messages);

        foreach ($data as $key => $value)
        {
            if (isset($rules[$key]))
            {
                $fields = explode('|', $rules[$key]);

                if(!$this->required($fields,$data,$key,$value))
                {
                    break;
                }

                foreach ($this->fields as $f)
                {
                    if (in_array($f, $fields)) {
                        if (!$this->is_mail($value)) {
                            $this->translation($key,['field' => $key]);
                        }
                    }
                }


                foreach ($fields as $field)
                {
                    if (strpos($field, ':'))
                    {
                        list($a, $b) = explode(':', $field, 2);

                        switch ($a)
                        {
                            case 'unique':
                                $control = DB::table($b)->where($key , $value)->first();
                                if ($control) {
                                    $this->translation('unique',['field' => $key]);
                                }
                                break;
                            case 'max':
                                if (mb_strlen($value) > $b) {
                                    $this->translation('max',['max' => $b]);
                                }
                                break;
                            case 'min':
                                if (mb_strlen($value) < $b) {
                                    $this->translation('min',['min' => $b]);
                                }
                                break;
                            case 'regex':
                                if (!preg_match("#^$b$#",$value)) {
                                    $this->translation('regex',['field' => $b]);
                                }
                                break;
                            case 'confirm':
                                if ($data[$b] != $value) {
                                    $this->translation('confirm', ['field' => $key,'confirm' => $b]);
                                }
                                break;

                            default:
                                //
                                break;
                        }

                    }
                }
            }
        }
        return $this ;
    }


    public function setMessage(Array $messages)
    {
        $this->messages = array_merge($this->messages,$messages);
    }


    private function required($fields,$data,$key,$value)
    {
        if (in_array('required', $fields))
        {
            if (is_array($data[$key]))
            {
                if (empty($value) || count($value) != count(array_filter($value)))
                {
                    foreach ($value as $k => $v)
                    {
                        if (empty($value[$k]) && $value[$k] !== 0)
                        {
                            $this->translation('required', ['field' => $key]);
                            return false;
                        }
                    }
                }
            }
            else
            {
                if (empty(trim($data[$key])))
                {
                    $this->translation('required', ['field' => $key]);
                }
            }
        }

        return true;
    }


    private function translation($field,$replace = [])
    {

        if(isset($this->translator[$field]))
        {
            if(!empty($replace))
            {
                $this->messages[$field][] = str_replace(
                    array_map(function($item){
                        return ":".$item;
                    },array_keys($replace)),
                    array_values($replace),
                    $this->translator[$field]
                );
            }
            else
            {
                $this->messages[$field][] = $this->translator[$field];
            }
        }
    }




    public function check():Bool
    {
        return !(count($this->messages) > 0) ;
    }



    public function messages():array
    {
        return $this->messages;
    }



    public function is_mail($email)
    {
        if (is_array($email)) {
            return false;
        }
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    }



    public function is_integer($value)
    {
        return is_integer($value);
    }



    public function is_numeric($value)
    {
        return is_numeric($value);
    }



    public function is_image($value):Bool
    {
        if (isset($value['tmp_name'])) {
            return \getimagesize($value['tmp_name']);
        } else {
            return \getimagesize($value);
        }

    }



    public function is_file($value):Bool
    {
        if (isset($value['tmp_name'])) {
            return is_file($value['tmp_name']);
        }
        return false;
    }



    public function is_url($url)
    {
        if (!is_string($url)) {
            return false;
        }
        return filter_var($url, FILTER_VALIDATE_URL);
    }



    public function is_ip($ip)
    {
        if (!is_string($ip)) {
            return false;
        }
        return filter_var($ip, FILTER_VALIDATE_IP);
    }


    public function __get($key)
    {
        return $this->messages[$key] ?? false;
    }

}
