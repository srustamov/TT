<?php namespace System\Libraries;



/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Language
 */

use System\Exceptions\LanguageException;
use System\Facades\Load;

class Language
{

    protected $lang;



    /**
     * @param Null $locale
     * @return bool
     */
    public function set ( $locale = null )
    {
        if(!is_null($locale))
        {
          if($this->locale() == $locale && !is_null($this->lang))
          {
            return $locale;
          }
          $locale = $this->locale($locale);
        }
        else
        {
          $locale = $this->locale();
        }

        $this->lang = $this->_getdata($locale);
   }

    /**
     * @param String $word
     * @param array $replace
     * @param Null $locale
     * @return String|array
     */

   public function translate ( String $word  ,array $replace = [] ,$locale = null)
   {

      if(is_null($this->lang))
      {
        $this->set();
      }


      if(!is_null($locale) && $locale != Load::class("session")->get('_LOCALE'))
      {
         $lang = $this->_getdata($locale);
      }

      if(strpos($word,'.') !== false)
      {
          list($file,$key) = explode('.',$word,2);

          if(!empty($replace))
          {
            return isset($this->lang[$file][$key])
                   ? str_replace(
                        array_map(function($item)
                        {
                           return ':'.$item;
                        },array_keys($replace)),array_values($replace),
                        $this->lang[$file][$key]
                     )
                   : '';
          }
          else
          {
            return $this->lang[$file][$key] ?? '';
          }
      }
      else
      {
        return $this->lang[$word] ?? '';
      }

   }



   public function data ($locale = false):array
   {
     return $this->_getdata($locale ?:$this->locale());
   }






   public function locale($locale = null):String
   {
      if(!is_null($locale))
      {
         Load::class('session')->set('_LOCALE',$locale);

         return $locale;
      }
      else
      {
        if($locale = Load::class('session')->get('_LOCALE'))
        {
           return $locale;
        }
        else
        {
           $locale  = Load::config('config.locale','en');

           Load::class('session')->set('_LOCALE',$locale);

           return $locale;
        }
      }



   }




    /**
     * @param String $locale
     * @throws LanguageException
     * @return array
     */

    private function _getdata(String $locale):array
    {
        $lang = [];

        if (is_dir (path('app/Language/'.$locale)))
        {
           foreach (glob(path("app/Language/{$locale}/*.ini")) as $file)
           {
             $lang[basename(substr($file,0,-4))] =  parse_ini_file($file);
           }
        }
        else
        {
          throw new LanguageException("Language folder not found. Path :".path('app/Language/'.$locale));
        }

        return $lang;
    }



}
