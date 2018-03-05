<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Libraries
 * @category    Validator
 */


use System\Facades\Language as VLang;

class Validator
{



    protected static $messages  = [];



    public function make(array $data, array $rules):Bool
    {
        foreach ($data as $key => $value) {
            if (isset($rules[$key])) {
                $fields = explode('|', $rules[$key]);
                if (in_array('required', $fields)) {
                    if (is_array($data[$key])) {
                        if (count($value) == 0 or count($value) != count(array_filter($value))) {
                            foreach ($value as $kk => $vv) {
                                if (empty($value[$kk]) && $value[$kk] !== 0) {
                                    static::$messages[$key][] = VLang::translate('validator.required', ['field' => $key]);
                                    break;
                                }
                            }
                        }
                    } else {
                        if (empty(trim($data[$key]))) {
                            static::$messages[$key][] = VLang::translate('validator.required', ['field' => $key]);
                        }
                    }
                }
                if (in_array('email', $fields)) {
                    if (!$this->is_mail($value)) {
                        static::$messages[$key][] = VLang::translate('validator.email');
                    }
                }
                if (in_array('integer', $fields)) {
                    if (!$this->is_integer($value)) {
                        static::$messages[$key][] = VLang::translate('validator.integer', ['field' => $key]);
                    }
                }
                if (in_array('numeric', $fields)) {
                    if (!$this->is_numeric($value)) {
                        static::$messages[$key][] = VLang::translate('validator.numeric', ['field' => $key]);
                    }
                }
                if (in_array('ip', $fields)) {
                    if (!$this->is_ip($value)) {
                        static::$messages[$key][] = VLang::translate('validator.ip', ['field' => $key]);
                    }
                }
                if (in_array('file', $fields)) {
                    if (!$this->is_file($value)) {
                        static::$messages[$key][] = VLang::translate('validator.file', ['field' => $key]);
                    }
                }
                if (in_array('image', $fields)) {
                    if (!$this->is_image($value)) {
                        static::$messages[$key][] = VLang::translate('validator.image', ['field' => $key]);
                    }
                }

                foreach ($fields as $field) {
                    if (strpos($field, ':')) {
                        list($a, $b) = explode(':', $field, 2);
                        switch ($a) {
                          case 'unique':
                            $control = app('db')->table($b)->where([$key => $value])->first();
                            if ($control) {
                                self::$messages[$key][] = VLang::translate('validator.unique', ['field' => $key]);
                            }
                            break;

                          case 'max':
                            if (mb_strlen($value) > $b) {
                                self::$messages[$key][] = VLang::translate('validator.max_character', ['max' => $b]);
                            }
                            break;

                          case 'min':
                            if (mb_strlen($value) < $b) {
                                self::$messages[$key][] = VLang::translate('validator.min_character', ['min' => $b]);
                            }
                            break;

                          case 'confirm':
                            if ($data[$b] != $value) {
                                static::$messages[$key][] = VLang::translate('validator.confirm', ['field' => $key,'confirm' => $b]);
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
        return !(count(self::$messages) > 0) ;
    }




    public function check():Bool
    {
        return !(count(static::$messages) > 0) ;
    }



    public function messages():array
    {
        return static::$messages;
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



    public function is_image($value)
    {
        if (isset($value['tmp_name']))
        {
            return @getimagesize($value['tmp_name']) ? true :  false;
        }
        return false;
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
      return self::$messages[$key] ?? false;
    }

}
