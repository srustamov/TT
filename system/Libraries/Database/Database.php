<?php namespace System\Libraries\Database;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Library
 * @category    Database
 */

use System\Libraries\Database\DatabaseInterface;
use System\Libraries\Database\Connection;
use PDO;
use PDOStatement;

class Database extends Connection
{


    private static $exception;

    private static $queryString;

    private $table;

    private $select = [];

    private $where  = [];

    private $limit  = [];

    private $orderBy= [];

    private $groupBy= [];

    private $set    = [];

    private $join   = [];

    private $database;

    private $execute_data = [];



    /**
     * @return bool|mixed
     */

    public function first ()
    {
      return $this->get(true);
    }



    public function table(String $table)
    {
      $this->table = static::$config[$this->connection_group]['prefix'].$table;
      return $this;
    }


    public function database(String $database)
    {
      $this->database = $database;
      return $this;
    }



    /**
     * @param array $array
     * @return $this
     */
    public function get($first = false)
    {
      $query = $this->getQueryString();

      static::$queryString =  $this->normalizeQueryString( $query );

      try
      {
        $statement = static::$connect->prepare($query);

        $this->bindValues($statement);

        $statement->execute();

        if($statement->rowCount() > 0)
        {
          return $first ? $statement->fetch() : $statement->fetchAll();
        }
        return null;
      }
      catch (\PDOException $e)
      {
        static::$exception = $e;
        return false;
      }


    }


    public function toArray($first = false)
    {
      if($result = $this->get($first))
      {
        return (array) $result;
      }
      else
      {
        return null;
      }
    }


    public function toJson($first = false)
    {
      if($result = $this->get($first))
      {
        return  json_encode($result);
      }
      else
      {
        return null;
      }
    }



    public function select($select)
    {
      if(is_array($select))
      {
        $select = implode(',',$select);
      }
      $this->select[] = $select;

      return $this;
    }



    public function where($column,$value = false,$mark = null,$logic = ["WHERE","AND"])
    {
      if(!is_null($mark))
      {
        $this->where[] = (empty($this->where) ? $logic[0] : $logic[1])." {$column} {$value} ? ";

        $this->execute_data[] = $mark;
      }
      elseif ($value == false)
      {
         if(is_array($column) && $this->array_is_assoc($column))
         {
           foreach ($column as $key => $value)
           {
             $this->where($key,$value);
           }
         }
         else
         {
           $this->where[] = (empty($this->where) ? $logic[0] : $logic[1])." ".$column." ";
         }
      }
      else
      {
        $this->where[] = (empty($this->where) ? $logic[0] : $logic[1])." ".$column." = ? ";
        $this->execute_data[] = $value;
      }

      return $this;

    }



    public function orWhere($column,$value = false,$mark = null)
    {
      return $this->where($column,$value,$mark,["WHERE","OR"]);
    }


    public function notWhere($column,$value = false,$mark = null)
    {
      return $this->where($column,$value,$mark,["WHERE NOT","AND NOT"]);
    }

    public function orNotWhere($column,$value = false,$mark = null)
    {
      return $this->where($column,$value,$mark,["WHERE NOT","OR NOT"]);
    }


    public function whereIn($column,$in,$logic = "")
    {
      $in = is_array($in) ? $in : explode(',',$in);

      $this->where[] = (empty($this->where) ? "WHERE " : " AND ").$column." {$logic} IN(".rtrim(str_repeat('?,',count($in)),',').")";

      $this->execute_data = array_merge($this->execute_data,$in);

      return $this;
    }



    public function whereNotIn($column,$in)
    {
      return $this->whereIn($column,$in,"NOT");
    }


    public function whereNull($column,$logic = "AND")
    {
      $this->where[] = (!empty($this->where) ? $logic : "WHERE")." {$column} IS NULL ";
      return $this;

    }



    public function whereNotNull($column,$logic = "AND")
    {
      $this->where[] = (!empty($this->where) ? $logic : "WHERE")." {$column} IS NOT NULL ";
      return $this;
    }



    public function orWhereNull($column)
    {
      return $this->whereNull($column,"OR");
    }



    public function orWhereNotNull($column)
    {
      return $this->WhereNotNull($column,"OR");
    }



    public function like($column,$like,$logic = "")
    {
      $this->where[] = (empty($this->where) ? "WHERE " : "AND ")."{$column} {$logic} LIKE ? ";
      $this->execute_data[] = $like;
      return $this;
    }



    public function notLike($column,$like)
    {
      return $this->like($column,$like,"NOT");
    }


    public function join ( String $table , $opt , $join = 'INNER' )
    {
        $this->join[] = strtoupper ( $join ) . ' JOIN ' . static::$config[$this->connection_group][ 'prefix' ] . $table . ' ON ' . $opt . ' ';
        return $this;
    }



    public function leftJoin ( String $table , $opt )
    {
        return $this->join ( $table , $opt , 'LEFT' );
    }



    public function rightJoin ( String $table , $opt )
    {
        return $this->join ( $table , $opt , 'RIGHT' );
    }


    public function fullJoin ( String $table , $opt )
    {
        return $this->join ( $table , $opt , 'FULL' );
    }


    public function limit ( $limit , $offset = 0 )
    {
        $this->limit[] = ' LIMIT ' . $offset . ',' . $limit;
        return $this;
    }


    public function orderBy ( $column , $sort = 'ASC' )
    {
        $this->orderBy[] = " ORDER BY " . $column . " " . strtoupper ( $sort );
        return $this;
    }

    public function groupBy ( $column )
    {
        $this->groupBy[] = ' GROUP BY ' . $column;
        return $this;
    }


    public function between ( $where , $start , $stop ,$mark = 'AND')
    {
      $this->where[] = empty($this->where) ? "WHERE " : "AND ".$where." BETWEEN ? {$mark} ? ";

      $this->execute_data = array_merge($this->execute_data,[$start,$stop]);

      return $this;
    }




    public function count($column = false)
    {
      $column = $column ? $column : implode('',$this->select);
      $this->select = array("COUNT({$column}) as count");
      if($result = $this->get(true))
      {
        return (int) $result->count;
      }
      else
      {
        return null;
      }
    }




    public function avg($column = false)
    {
      $column = $column ? $column : implode('',$this->select);
      $this->select = array("AVG({$column}) as avg");
      if($result = $this->get(true)) {
        return $result->avg;
      } else {
        return null;
      }
    }



    public function set($set)
    {
      if (is_array ( $set ))
      {
        if ($this->array_is_assoc( $set ))
        {
            $this->execute_data = array_merge ( $this->execute_data,array_values ( $set ) );
            $set = array_keys($set);
        }
      }
      else
      {
        $set = explode ( ',' , $set );
      }

      foreach ($set as $key => $value)
      {
          $this->set[] = $value . " =? ";
      }

      return $this;
    }


    public function insert(Array $data = [])
    {
      if($this->array_is_assoc($data))
      {
        $this->set($data);
      }
      else
      {
        $this->execute_data = array_merge($this->execute_data,$data);
      }


      $set = '';

      if(!empty($this->set))
      {
        $set = " SET ".implode(',',$this->set);
      }
      $query = preg_replace("/SELECT.*FROM {$this->table}/", '', $this->getQueryString() , 1);

      static::$queryString =  $this->normalizeQueryString("INSERT INTO {$this->table} {$set} {$query}");

      $statement = static::$connect->prepare("INSERT INTO {$this->table} {$set} {$query}");


      try
      {
          static::$connect->beginTransaction ();

          $this->bindValues($statement);
          $statement->execute ();
          static::$connect->commit ();

          return $statement->rowCount ();
      }
      catch (\PDOException $e)
      {
          static::$exception = $e;
          static::$connect->rollBack ();
          return false;
      }
    }


    public function update(Array $data = [])
    {

      if($this->array_is_assoc($data))
      {
        $this->set($data);
      }
      else
      {
        $this->execute_data = array_merge($this->execute_data,$data);
      }


      $set = '';
      if(!empty($this->set)) {
        $set = " SET ".implode(',',$this->set);
      }
      $query = preg_replace("/SELECT.*FROM {$this->table}/", '', $this->getQueryString() , 1);

      static::$queryString =  $this->normalizeQueryString("UPDATE  {$this->table} {$set} {$query}");

      $statement = static::$connect->prepare("UPDATE  {$this->table} {$set} {$query}");

      try
      {
          //static::$connect->beginTransaction ();
          $this->bindValues($statement);
          $statement->execute ();
          //static::$connect->commit ();
          return $statement->rowCount() > 0;
      }
      catch (\PDOException $e)
      {
          static::$exception = $e;
          //static::$connect->rollBack ();
          return false;
      }
    }


    public function delete()
    {

      $query = preg_replace("/SELECT.*FROM/", 'DELETE FROM', $this->getQueryString() , 1);

      static::$queryString =  $this->normalizeQueryString($query);

      $statement = static::$connect->prepare($query);

      try
      {
          static::$connect->beginTransaction ();
          $this->bindValues($statement);
          $statement->execute ();
          static::$connect->commit ();
          return $statement->rowCount();
      }
      catch (\PDOException $e)
      {
          static::$exception = $e;
          static::$connect->rollBack ();
          return false;
      }
    }



    /**
     * @param $table
     * @return bool|string
     */
    public function optimizeTables ( $tables = '*')
    {
        if (trim($tables) == '*')
        {
          $tables = $this->showTables();
        }
        else
        {
          if (is_array($tables))
          {
            $tables = array_map(function($item){
                        return static::$config[$this->connection_group]['prefix'].$item;
                      },$tables);
          }
          else
          {
            $tables = explode ( ',' , $tables );
            $tables = array_map(function($item){
                        return static::$config[$this->connection_group]['prefix'].$item;
                      },$tables);
          }
        }
        $success = true;

        foreach ($tables as $table)
        {
          $success = static::$connect->exec ( "OPTIMIZE TABLE {$table}") === false ? false : true;
        }

        return $success ? 'Optimize Tables Success' : $success;
    }



    /**
     * @return array|bool
     */
    public function showTables ()
    {
      $result = static::$connect->query("SHOW TABLES")->fetchAll(PDO::FETCH_ASSOC);

      return array_map(function($item){
                  return array_values($item)[0];
                },$result);
    }

    /**
     * @param $tables
     * @return Bool|String
     */
    public function repairTables ( $tables = '*' )
    {
      if($this->table == '')
      {
        if (trim($tables) == '*')
        {
          $tables = $this->showTables();
        }
        else
        {
          if (is_array($tables))
          {
            $tables = array_map(function($item){
                        return static::$config[$this->connection_group]['prefix'].$item;
                      },$tables);
          }
          else
          {
            $tables = explode ( ',' , $tables );
            $tables = array_map(function($item){
                        return static::$config[$this->connection_group]['prefix'].$item;
                      },$tables);
          }
        }
      }
      else
      {
        $tables = array($this->table);
      }

      $success = true;

      foreach ($tables as $table)
      {
         $success = static::$connect->exec ( "REPAIR TABLE {$table}") === false ? false : true;
      }

      return $success ? 'Repair Tables Success' : $success;
    }



    /**
     * Drop Table or database
     * @return bool
     */
    public function drop ()
    {
        $drop = !is_null($this->table) ? " TABLE "    : $this->database ? " DATABASE " : "";

        $item = !is_null($this->table) ? $this->table : $this->database ? " DATABASE " : "";

        try
        {
            return static::$connect->exec ( "DROP {$drop} IF EXISTS {$item}") === false ? false : true;
        }
        catch (\PDOException $e)
        {
            static::$exception = $e;
            return false;
        }


    }

    /**
     * Truncate table
     * @return bool
     */
    public function truncate ()
    {

        static::$queryString = "TRUNCATE TABLE IF EXISTS {$this->table} ";

        try
        {
          return static::$connect->exec (static::$queryString) === false ? false : true;
        }
        catch (\PDOException $e)
        {
          static::$exception = $e;
          return false;
        }

    }

    /**
     * @param $dbname
     * @return array|bool
     */
    public function list_tables ()
    {
        $database = $this->database ?: static::$config[$this->connection_group][ 'dbname' ];
        static::$queryString = "SHOW TABLES FROM {$database}";
        try
        {
          $result = static::$connect->query (static::$queryString);
          if ($result->rowCount () > 0)
          {
              return $result->fetchAll ();
          }
          return null;
        }
        catch (\PDOException $e)
        {
          static::$exception = $e;
          return false;
        }

    }


    /**
     * @param $table
     * @return array|bool
     */
    public function list_columns ()
    {
        static::$queryString = "SHOW COLUMNS FROM {$this->table}";
        try
        {
            $result = static::$connect->query ( static::$queryString );
            if ($result->rowCount () > 0)
            {
                return $result->fetchAll ();
            }
            return null;
        }
        catch (\PDOException $e)
        {
          static::$exception = $e;
          return false;
        }


    }


    /**
     * @return mixed
     */

    public function lastId ()
    {
        return static::$connect->lastInsertId();
    }


    /**
     * @param $data
     * @return string
     */
    public function escape ($data)
    {
        if (is_array ($data))
        {
          foreach ($data as $key => $value)
          {
              $data[ $key ] = static::$connect->quote ( trim ( $value ) );
          }
        }
        else
        {
            $data = static::$connect->quote (trim($data));
        }

        return $data;
    }


    /**
     * @return int
     */
    public function last_query ()
    {
        return static::$queryString;
    }



    private function getQueryString()
    {
      if (empty($this->select))
      {
        $this->select[] = "*";
      }

      $query  = "SELECT ".implode(',',$this->select)." FROM ".$this->table." ";
      $query .= implode(' ',$this->join);
      $query .= implode(' ',$this->where);
      $query .= implode(' ',$this->orderBy);
      $query .= implode(' ',$this->groupBy);
      $query .= implode(' ',$this->limit);

      return $query;

    }




    public function reset ()
    {
        $this->execute_data = [];
        $this->select  = [];
        $this->where   = [];
        $this->limit   = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->set     = [];
        $this->join    = [];
        $this->table   = null;
        $this->database= null;
        static::$queryString = null;
        static::$lastId    = null;
        static::$exception = null;

    }


    public function bindValues(PDOStatement $statement)
    {
        foreach ($this->execute_data as $key => $value)
        {
            $statement->bindValue($key + 1, $value,
                is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
    }


    private function normalizeQueryString($query)
    {
      foreach ($this->execute_data as $value)
      {
        $position = strpos($query,'?');
        if($position !== false)
        {
           $query =  substr_replace ( $query , $value , $position , 1);
        }
      }
      return $query;
    }


    private function array_is_assoc($array)
    {
      if(is_array($array))
      {
        $keys = array_keys($array);
        return (array_keys($keys) !== $keys);
      }
      else
      {
        return false;
      }
    }

    public function __call($method,$args)
    {
      return static::$connect->{$method}($args);
    }


    public static function __callStatic($method,$args)
    {
      return static::$connect->{$method}($args);
    }

    /**
     * @return string
     */

    public function __toString ()
    {
        return 'Database Library';
    }


    private function __clone ()
    {
    }



    function __destruct ()
    {
      if (!is_null(static::$exception))
      {
          return show_error('Database Message:'.static::$exception->getmessage(),__FILE__,static::$exception->getline());
      }
    }


}
