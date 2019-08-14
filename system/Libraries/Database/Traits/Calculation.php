<?php namespace System\Libraries\Database\Traits;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Database
 */


 trait Calculation
 {
     public function count($column = false)
     {
         $column = $column ? $column : implode('', $this->select);

         $this->select = array("COUNT({$column}) as count");

         if ($result = $this->get(true)) {
             return (int)$result->count;
         } else {
             return null;
         }
     }

     public function min(string $column)
     {
         $as_name = 'min_'.$column;
         $this->select = array("MIN({$column}) as {$as_name}");

         if ($result = $this->get(true)) {
             return (int) $result->$as_name;
         } else {
             return null;
         }
     }

     public function max(string $column)
     {
         $as_name = 'min_'.$column;

         $this->select = array("MAX({$column}) as {$as_name}");

         if ($result = $this->get(true)) {
             return (int) $result->$as_name;
         } else {
             return null;
         }
     }

     public function avg($column = false)
     {
         $column = $column ? $column : implode('', $this->select);

         $this->select = array("AVG({$column}) as avg");

         if ($result = $this->get(true)) {
             return $result->avg;
         } else {
             return null;
         }
     }

     public function sum($column = false)
     {
         $column = $column ? $column : implode('', $this->select);

         $this->select = array("SUM({$column}) as sum");

         if ($result = $this->get(true)) {
             return $result->sum;
         } else {
             return null;
         }
     }
 }
