<?php namespace System\Libraries\Database\Traits;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Database
 */


 trait Join
 {
     public function leftJoin(String $table, $opt)
     {
         return $this->join($table, $opt, 'LEFT');
     }

     public function join(String $table, $opt, $join = 'INNER')
     {
         $this->join[] = strtoupper($join) . ' JOIN ' . $this->config[$this->group]['prefix'] . $table . ' ON ' . $opt . ' ';
         return $this;
     }

     public function rightJoin(String $table, $opt)
     {
         return $this->join($table, $opt, 'RIGHT');
     }

     public function fullJoin(String $table, $opt)
     {
         return $this->join($table, $opt, 'FULL');
     }
 }
