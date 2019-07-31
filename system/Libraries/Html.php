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
        return htmlspecialchars(
            trim(html_entity_decode($str, ENT_QUOTES)),
            ENT_QUOTES,
            'UTF-8',
            false
        );
    }


    public function clean(String $data):String
    {
        return strip_tags(
            htmlentities(trim(stripslashes($data)), ENT_NOQUOTES, 'UTF-8')
        );
    }


    public function FullSpecial(String $str):String
    {
        return filter_var($str, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }


    public function css(String $file, $modified = false):String
    {
        if ($modified) {
            $file .= '?v=' . @filemtime(public_path($file));
        }

        return '<link rel="stylesheet" type="text/css"  href="' . url($file) . '">';
    }



    public function js(String $file, $modified = false):String
    {
        if ($modified) {
            $file .= '?v=' . @filemtime(public_path($file));
        }

        return  '<script type="text/javascript" src="' . url($file) . '"></script>';
    }



    public function img(string $file, $attributes = []): string
    {
        $img  = '<img src="'.url($file).'" ';

        foreach ($attributes as $key => $value) {
            $img .= $key.'='."\"$value\" ";
        }

        return $img.' />';
    }


    public function link($content, $href, $attributes = []): string
    {
        $link = '<a href="'.$href.'" ';

        foreach ($attributes as $key => $value) {
            $link .= $key.'="'.$value.'" ';
        }

        return $link.">$content</a>";
    }


    public function __call($method, $arguments)
    {
        $once  = array('meta','img','link','br','hr');

        $content = $arguments[0] ?? false;

        $attributes = $arguments[1] ?? [];

        if (in_array($method, $once, true)) {
            $attributes = $arguments[0] ?? [];

            $tag = "<{$method} ";

            if (is_array($attributes)) {
                foreach ($attributes as $key => $value) {
                    $tag .= $key.'="'.$value.'" ';
                }

                return $tag. '/>';
            }

            return $tag.$attributes. '/>';
        }

        $tag = "<{$method} ";

        if (is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $tag .= $key.'="'.$value.'" ';
            }

            return $tag.">{$content}</{$method}>";
        }

        return $tag.$attributes.">{$content}<{$method}>";
    }
}
