<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Html
 */



 class Html
 {


     public function filter(String $str):String
     {
         return htmlspecialchars(trim(html_entity_decode($str, ENT_QUOTES)), ENT_QUOTES, 'UTF-8', false);
     }


     public function clean(String $data):String
     {
         return strip_tags(htmlentities(trim(stripslashes($data)), ENT_NOQUOTES, "UTF-8"));
     }


     public function FullSpecial(String $str):String
     {
         return filter_var($str, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
     }


     public function css(String $file ,$modified = true):String
     {
         if ($modified)
         {
             $file = $file . '?v=' . @filemtime(public_dir($file));
         }

         return '<link rel="stylesheet" type="text/css"  href="' . url($file) . '">';
     }



     public function js(String $file,$modified = true):String
     {
         if ($modified)
         {
             $file = $file . '?v=' . @filemtime(public_dir($file));
         }

         return  '<script type="text/javascript" src="' . url($file) . '"></script>';
     }



     public function img(String $file , $attributes = []):String
     {

         $img  = '<img src="'.url($file).'" ';

         foreach ($attributes as $key => $value)
         {
           $img .= $key.'='."\"$value\" ";
         }

         return $img.' />';
     }


 }
