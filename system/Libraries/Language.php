<?php namespace System\Libraries;



/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Libraries
 * @category    Language
 */

use Exception as LanguageException;


class Language
{



    protected static $lang;



    /**
     * @param Null $locale
     * @return bool
     */
    public function set ( $locale = null )
    {
        if(!is_null($locale))
        {
          if($this->locale() == $locale && !is_null(static::$lang))
          {
            return $locale;
          }
          $locale = $this->locale($locale);
        }
        else
        {
          $locale = $this->locale();
        }

        static::$lang = $this->_getdata($locale);
   }

   /**
    * @param String $word
    * @param Array $replace
    * @param Null $locale
    * @return String
    */


   public function translate ( $word = '' ,$replace = [] ,$locale = null):String
   {

      if(is_null(static::$lang))
      {
        $this->set();
      }

      $lang = &static::$lang;

      if(!is_null($locale) && $locale != app("session")->get('_LOCALE'))
      {
         $lang = $this->_getdata($locale);
      }

      if(strpos($word,'.') !== false)
      {
          list($file,$key) = explode('.',$word,2);

          if(!empty($replace))
          {
            return isset($lang[$file][$key])
                   ? str_replace(
                        array_map(function($item)
                        {
                           return ':'.$item;
                        },array_keys($replace)),array_values($replace),
                        $lang[$file][$key]
                     )
                   : '';
          }
          else
          {
            return $lang[$file][$key] ?? '';
          }
      }
      else
      {
        return $lang[$word] ?? '';
      }

   }



   public function data ($locale = false):Array
   {
     return $this->_getdata($locale ?:$this->locale());
   }






   public function locale($locale = null):String
   {
      if(!is_null($locale))
      {
         app('session')->set('_LOCALE',$locale);

         return $locale;
      }
      else
      {
        if($locale = app('session')->get('_LOCALE'))
        {
           return $locale;
        }
        else
        {
           $locale  = config('config.locale','en');

           app('session')->set('_LOCALE',$locale);

           return $locale;
        }
      }



   }




    /**
     * @param String $locale
     * @throws LanguageException
     * @return Array
     */

    private function _getdata(String $locale):Array
    {
        $lang = [];

        if (is_dir (app_dir('Language/'.$locale)))
        {
           foreach (glob(app_dir("Language/{$locale}/*\.ini")) as $file)
           {
             $lang[basename(substr($file,0,-4))] =  parse_ini_file($file);
           }
        }
        else
        {
          throw new LanguageException("Language folder not found. Path :".app_dir('Language/'.$locale));
        }

        return $lang;
    }



}
