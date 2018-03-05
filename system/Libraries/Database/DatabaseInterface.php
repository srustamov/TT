<?php namespace System\Libraries\Database;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Libraries
 * @category    Database
 */


interface DatabaseInterface
{

  /**
   * @param Bool $first
   * @return Bool|Array
   */

  public function first ();



  public function table(String $table);



  public function database(String $database);



  public function get($first = false);



  public function select($select);



  public function where($column,$value = false,$mark = null);



  public function whereIn($column,$in);



  public function whereNotIn($column,$in);



  public function notWhere($column,$value = false,$mark = null);



  public function orWhere($column,$value = false,$mark = null);



  public function like($column,$like);



  public function notLike($column,$like);



  public function join ( String $table , $opt , $join = 'INNER' );



  public function leftJoin ( String $table , $opt );



  public function rightJoin ( String $table , $opt );



  public function fullJoin ( String $table , $opt );



  public function limit ( $limit , $offset = 0 );



  public function orderBy ( $column , $sort = 'ASC' );



  public function groupBy ( $column );



  public function between ( $where , $start , $stop ,$mark = 'AND');



  public function count($column = false);



  public function avg($column = false);



  public function set($set);



  public function insert(Array $data = []);




  public function update(Array $data = []);


  /**
   * @param $table
   * @return bool|string
   */
  public function optimizeTables ( $tables = '*');


  /**
   * @return array|bool
   */
  public function showTables ();

  /**
   * @param $table
   * @return bool|string
   */
  public function repairTables ( $tables = '*' );


  /**
   * Drop Table or database
   * @return bool
   */
  public function drop ();

  /**
   * Truncate table
   * @return bool
   */
  public function truncate ();

  /**
   * @param $dbname
   * @return array|bool
   */
  public function list_tables ();


  /**
   * @param $table
   * @return array|bool
   */
  public function list_columns ();


  /**
   * @return mixed
   */

  public function lastId ();


  /**
   * @param $data
   * @return string
   */
  public function escape ( $data );


  /**
   * @return
   */
  public function last_query ();



  function _getQuery();



  public function reset ();






}
