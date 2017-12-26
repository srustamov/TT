<?php namespace System\Libraries;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Libraries
 * @category    String
 */


class Str
{


    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @return mixed
     */
    public function replace_first ( $search , $replace , $subject )
    {
        $position = strpos ( $subject , $search );

        if ($position !== false) {
            return substr_replace ( $subject , $replace , $position , strlen ( $search ) );
        }

        return $subject;
    }



    public function random($length = 6 ,$type = null):String
    {
      $result   = '';
      $alpha    = 'ABCDEFGHIJKLMNOQPRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
      $numeric  = '1234567890';

      if(is_null($type)) {
        $all = $alpha.$numeric;
      } elseif ('alpha') {
        $all = $alpha;
      } elseif ('numeric') {
        $all = $numeric;
      }

      for( $i = 0; $i < $length; $i++ )
      {
          $result .= substr( $all, rand( 0, strlen($all)), 1 );
      }

      return $result;
    }


    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @return mixed
     */
    public function replace_last ( $search , $replace , $subject )
    {
        $position = strrpos ( $subject , $search );

        if ($position !== false) {
            return substr_replace ( $subject , $replace , $position , strlen ( $search ) );
        }

        return $subject;
    }


    /**
     * @param $str
     * @param string $separator
     * @return String
     */
    public function slug ( $str , $separator = '-' ): String
    {
        $str = implode ( $separator , array_filter ( explode ( ' ' , $str ) ) );
        return $this->lower ( $str , 'UTF-8' );
    }


    /**
     * @param $value
     * @param Int $limit
     * @param String $end
     * @return String
     */
    public function limit($value, $limit = 100, $end = '...'):String
    {
      if($this->length($value) > $limit) {
        return mb_substr($value, 0 , $limit,"UTF-8").$end;
      }
      return $value;
    }


    /**
     * @param $str
     * @return String
     */
    public function upper ( $str ): String
    {
        return mb_strtoupper ( $str , 'UTF-8' );
    }


    /**
     * @param $str
     * @return String
     */
    public function lower ( $str ): String
    {
        return mb_strtolower ( $str , 'UTF-8' );
    }


    /**
     * @param $str
     * @return String
     */
    public function title ( $str ): String
    {
        return mb_convert_case ( $str , MB_CASE_TITLE , 'UTF-8' );
    }


    /**
     * @param $value
     * @param null $encoding
     * @return int
     */
    public function length ( $value , $encoding = null )
    {
        if ($encoding) {
            return mb_strlen ( $value , $encoding );
        }

        return mb_strlen ( $value );
    }


    /**
     * @param $search
     * @param array $replace
     * @param $subject
     * @return String
     */
    public function replace_array ( $search , array $replace , $subject ): String
    {
        foreach ($replace as $value) {
            $subject = str_replace_first ( $search , $value , $subject );
        }

        return $subject;
    }



    public function FullTrim ( $str , $char = ' ' )
    {
        return str_replace ( $char , '' , $str );
    }


}
