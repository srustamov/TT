<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Language
 */


use System\Engine\App;

class Language implements \ArrayAccess
{
    protected $languages = [];


    public function __construct()
    {
        $this->prepare();
    }


    public function prepare()
    {
        $locale = $this->locale();

        $app = App::instance();

        foreach (glob($app->langPath($locale.'/*')) as $file) {
            $this->languages[pathinfo($file, PATHINFO_FILENAME)] = require_once($file);
        }
    }


    /**
     * @param String $word
     * @param array $replace
     * @return array|String
     * @internal param Null $locale
     */
    public function translate(String $word, array $replace = [])
    {
        if (strpos($word, '.') !== false) {
            $data = $this->get($word);

            if (is_null($data) || empty($replace)) {
                return $data;
            }

            $keys = array_map(function ($key) {
                return ':'.$key;
            }, array_keys($replace));

            $values = array_values($replace);

            return str_replace($keys, $values, $data);
        } else {
            return $this->languages[$word] ?? '';
        }
    }


    public function get($key, $default = null)
    {
        if (strpos($key, '.')) {
            $item_recursive = explode('.', $key);

            $lang = $this->languages;

            foreach ($item_recursive as $item) {
                $lang = $lang[$item] ?? false;
            }

            return $lang ?: $default;
        } else {
            return $this->languages[$key] ?? $default;
        }
    }



    public function all():array
    {
        return $this->languages;
    }






    public function locale($locale = null):String
    {
        if (!is_null($locale)) {
            App::get('session')->set('_LOCALE', $locale);

            return $locale;
        } else {
            if ($locale = App::get('session')->get('_LOCALE')) {
                return $locale;
            } else {
                $locale  = App::get('config')->get('app.locale', 'en');

                App::get('session')->set('_LOCALE', $locale);

                return $locale;
            }
        }
    }



    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->languages[$offset]  = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if (isset($this->languages[$offset])) {
            unset($this->languages[$offset]);
        }
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->languages[$offset]);
    }
}
