<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Library
 * @category    Html
 */



 class Html
 {
     public function filter(String $str):String
     {
         $str = html_entity_decode($str, ENT_QUOTES);
         return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8', false);
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
         $file = ltrim($file, '/');

         $dir = BASEDIR . DS . 'public/' .$file;

         $file = '/'.$file . '?v=' . @filemtime($dir);

         return '<link rel="stylesheet" type="text/css"  href="' . $file . '">';
     }



     public function js(String $file,$modified = true):String
     {
         $file = ltrim($file, '/');

         $dir = BASEDIR . DS . 'public/' .$file;

         $file = '/'.$file . '?v=' . @filemtime($dir);

         return  '<script type="text/javascript" src="' . $file . '"></script>';
     }



     public function img(String $file , $attributes = []):String
     {
         $file = ltrim($file, '/');
         $img  = '<img src="'. '/'.$file .'" ';

         foreach ($attributes as $key => $value)
         {
           $img .= $key.'='."\"$value\" ";
         }

         return $img.' />';
     }




     public function BootstrapCss():String
     {
         return '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">';
     }



     public function BootstrapJs():String
     {
         return '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
     }



     public function FontAwesome():String
     {
         return '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">';
     }



     public function jquery():String
     {
         return  '<script src="https://code.jquery.com/jquery-latest.js"></script>';
     }



     public function JqueryUi():String
     {
         return '<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.js"></script>';
     }



     public function angular():String
     {
         return '<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.29/angular.min.js"></script>';
     }
 }
