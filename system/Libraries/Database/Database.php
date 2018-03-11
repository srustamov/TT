<?php namespace System\Libraries\Database;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Database
 */


use PDO;
use PDOStatement;
use System\Facades\Cache;

class Database extends Connection
{


    private $table;

    private $select  = [];

    private $where   = [];

    private $limit   = [];

    private $orderBy = [];

    private $groupBy = [];

    private $join    = [];

    private $database;

    private $execute_data = [];




    /**
     * @return object|null
     */

    public function first ()
    {
        return $this->get ( true );
    }

    /**
     * @param bool $first
     * @return null|object
     * @throws \System\Libraries\Database\DatabaseException
     */
    public function get ( $first = false ,$fetch = PDO::FETCH_OBJ)
    {


        $query = $this->getQueryString ();

        $queryString = $this->normalizeQueryString ( $query );

        if ($first) $query .= " LIMIT 1";

        try {
            $statement = $this->pdo->prepare ( $query );

            $this->bindValues ( $statement );

            $statement->execute ();

            $this->reset();

            if ($statement->rowCount () > 0) {
                return $first ? $statement->fetch ($fetch) : $statement->fetchAll ($fetch);
            } else {
                return null;
            }

        } catch (\PDOException $e) {

            throw new DatabaseException($e->getMessage()."<br />"."[QUERY: $queryString] ");
        }


    }



    /**
     * @return string
     */
    private function getQueryString ()
    {
        if (empty( $this->select )) {
            $this->select[] = "*";
        }

        $query = "SELECT " . implode ( ',' , $this->select ) . " FROM " . $this->table . " ";
        $query .= implode ( ' ' , $this->join );
        $query .= implode ( ' ' , $this->where );
        $query .= implode ( ' ' , $this->orderBy );
        $query .= implode ( ' ' , $this->groupBy );
        $query .= implode ( ' ' , $this->limit );

        return $query;

    }

    private function normalizeQueryString ( $query )
    {
        foreach ($this->execute_data as $value) {
            $position = strpos ( $query , '?' );
            if ($position !== false) {
                $query = substr_replace ( $query , $value , $position , 1 );
            }
        }
        return $query;
    }

    public function bindValues ( PDOStatement $statement )
    {
        foreach ($this->execute_data as $key => $value) {
            $statement->bindValue ( $key + 1 , $value ,
                is_int ( $value ) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
    }

    public function table ( String $table )
    {
        $this->table = $this->config[ $this->group ][ 'prefix' ] . $table;
        return $this;
    }

    public function database ( String $database )
    {
        $this->database = $database;
        return $this;
    }

    public function toArray ( $first = false )
    {
        if ($result = $this->get ( $first ,PDO::FETCH_ASSOC)) {
            return $result;
        } else {
            return null;
        }
    }

    public function toJson ( $first = false )
    {
        if ($result = $this->get ( $first )) {
            return json_encode ( $result );
        } else {
            return null;
        }
    }

    public function select ( $select )
    {
        if (is_array ( $select )) {
            $select = implode ( ',' , $select );
        }
        $this->select = [ $select ];

        return $this;
    }

    public function orWhere ( $column , $value = false , $mark = null )
    {
        return $this->where ( $column , $value , $mark , [ "WHERE" , "OR" ] );
    }

    public function where ( $column , $value = false , $mark = null , $logic = [ "WHERE" , "AND" ] )
    {
        if (!is_null ( $mark )) {
            $this->where[] = ( empty( $this->where ) ? $logic[ 0 ] : $logic[ 1 ] ) . " {$column} {$value} ? ";

            $this->execute_data[] = $mark;
        } elseif ($value == false) {
            if (is_array ( $column ) && $this->array_is_assoc ( $column )) {
                foreach ($column as $key => $value) {
                    $this->where ( $key , $value );
                }
            } else {
                $this->where[] = ( empty( $this->where ) ? $logic[ 0 ] : $logic[ 1 ] ) . " " . $column . " ";
            }
        } else {
            $this->where[] = ( empty( $this->where ) ? $logic[ 0 ] : $logic[ 1 ] ) . " " . $column . " = ? ";
            $this->execute_data[] = $value;
        }

        return $this;

    }

    private function array_is_assoc ( $array )
    {
        if (is_array ( $array )) {
            $keys = array_keys ( $array );
            return ( array_keys ( $keys ) !== $keys );
        } else {
            return false;
        }
    }

    public function notWhere ( $column , $value = false , $mark = null )
    {
        return $this->where ( $column , $value , $mark , [ "WHERE NOT" , "AND NOT" ] );
    }

    public function orNotWhere ( $column , $value = false , $mark = null )
    {
        return $this->where ( $column , $value , $mark , [ "WHERE NOT" , "OR NOT" ] );
    }

    public function whereNotIn ( $column , $in )
    {
        return $this->whereIn ( $column , $in , "NOT" );
    }

    public function whereIn ( $column , $in , $logic = "" )
    {
        $in = is_array ( $in ) ? $in : explode ( ',' , $in );

        $this->where[] = ( empty( $this->where ) ? "WHERE " : " AND " ) . $column . " {$logic} IN(" . rtrim ( str_repeat ( '?,' , count ( $in ) ) , ',' ) . ")";

        $this->execute_data = array_merge ( $this->execute_data , $in );

        return $this;
    }

    public function orWhereNull ( $column )
    {
        return $this->whereNull ( $column , "OR" );
    }

    public function whereNull ( $column , $logic = "AND" )
    {
        $this->where[] = ( !empty( $this->where ) ? $logic : "WHERE" ) . " {$column} IS NULL ";
        return $this;

    }

    public function orWhereNotNull ( $column )
    {
        return $this->WhereNotNull ( $column , "OR" );
    }

    public function whereNotNull ( $column , $logic = "AND" )
    {
        $this->where[] = ( !empty( $this->where ) ? $logic : "WHERE" ) . " {$column} IS NOT NULL ";
        return $this;
    }

    public function notLike ( $column , $like )
    {
        return $this->like ( $column , $like , "NOT" );
    }

    public function like ( $column , $like , $logic = "" )
    {
        $this->where[] = ( empty( $this->where ) ? "WHERE " : "AND " ) . "{$column} {$logic} LIKE ? ";
        $this->execute_data[] = $like;
        return $this;
    }

    public function leftJoin ( String $table , $opt )
    {
        return $this->join ( $table , $opt , 'LEFT' );
    }

    public function join ( String $table , $opt , $join = 'INNER' )
    {
        $this->join[] = strtoupper ( $join ) . ' JOIN ' . $this->config[ $this->group ][ 'prefix' ] . $table . ' ON ' . $opt . ' ';
        return $this;
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

    public function orderByRand ()
    {
        $this->orderBy[] = " ORDER BY RAND() ";
        return $this;
    }

    public function groupBy ( $column )
    {
        $this->groupBy[] = ' GROUP BY ' . $column;
        return $this;
    }

    public function between ( $where , $start , $stop , $mark = 'AND' )
    {
        $this->where[] = empty( $this->where ) ? "WHERE " : "AND " . $where . " BETWEEN ? {$mark} ? ";

        $this->execute_data = array_merge ( $this->execute_data , [ $start , $stop ] );

        return $this;
    }

    public function count ( $column = false )
    {
        $column = $column ? $column : implode ( '' , $this->select );
        $this->select = array( "COUNT({$column}) as count" );
        if ($result = $this->get ( true )) {
            return (int) $result->count;
        } else {
            return null;
        }
    }

    public function avg ( $column = false )
    {
        $column = $column ? $column : implode ( '' , $this->select );
        $this->select = array( "AVG({$column}) as avg" );
        if ($result = $this->get ( true )) {
            return $result->avg;
        } else {
            return null;
        }
    }

    public function sum ( $column = false )
    {
        $column = $column ? $column : implode ( '' , $this->select );
        $this->select = array( "SUM({$column}) as sum" );
        if ($result = $this->get ( true )) {
            return $result->sum;
        } else {
            return null;
        }
    }

    public function insert ( $insert, Array $data = [] )
    {
        if(is_string($insert)) {
            $query = $insert;
            $this->execute_data = $data;
        } else {
            if(is_array($insert)) {
                if($this->array_is_assoc($insert)) {
                    $query = "INSERT INTO {$this->table} SET ".
                        implode(',',array_map(function ($item){
                            return $item."=?";
                        },array_keys($insert)));
                    $this->execute_data = array_values($insert);
                }
            }
        }

        $queryString = $this->normalizeQueryString($query);

        try {
          $statement = $this->pdo->prepare ( $query );
          $this->bindValues ( $statement );
          $statement->execute ();
          $this->reset();
          return $statement->rowCount () > 0;
        } catch (\PDOException $e) {
            throw new DatabaseException
            (
              "[INSERT] ".$e->getMessage()."<br />".
              "[QUERY: $queryString]"
            );
        }
    }


    public function update ( $update ,Array $data = [] )
    {

        if(is_string($update)) {
            $query = $update;
            $this->execute_data = $data;
        } else {
            if(is_array($update)) {
                if($this->array_is_assoc($update)) {
                    $query = "UPDATE {$this->table} SET ".
                        implode(',',array_map(function ($item){
                            return $item."=?";
                        },array_keys($update))).
                        preg_replace ( "/SELECT.*FROM {$this->table}/" , '' , $this->getQueryString () , 1 );
                    $this->execute_data = array_merge(array_values($update),$this->execute_data);
                }
            }
        }

        $queryString = $this->normalizeQueryString($query);

        try {
          $statement = $this->pdo->prepare ( $query );
          $this->bindValues ( $statement );
          $statement->execute ();
          $this->reset();
          return $statement->rowCount () > 0;
        } catch (\PDOException $e) {
            throw new DatabaseException
            (
              "[UPDATE] ".$e->getMessage()."<br />".
              "[QUERY: $queryString]"
            );
        }
    }

    public function delete ($delete = null,Array $data = [])
    {

        if(is_string($delete)) {
            $query = $delete;
            $this->execute_data = $data;
        } else {
            if(is_array($delete)) {
                if($this->array_is_assoc($delete)) {
                    $this->where($delete);
                }
            } else {
              $query = "DELETE FROM {$this->table} ".
                  preg_replace (
                      "/SELECT.*FROM {$this->table}/" , '' ,
                          $this->getQueryString () , 1
                  );
            }
        }

        $queryString = $this->normalizeQueryString($query);

        try {
          $statement = $this->pdo->prepare ( $query );
          $this->bindValues ( $statement );
          $statement->execute ();
          $this->reset();
          return $statement->rowCount () > 0;
        } catch (\PDOException $e) {
            throw new DatabaseException
            (
              "[DELETE] ".$e->getMessage()."<br />".
              "[QUERY: $queryString]"
            );
        }
    }

    /**
     * @param string $tables
     * @return bool|string
     * @internal param $table
     */
    public function optimizeTables ( $tables = '*' )
    {
        if (trim ( $tables ) == '*') {
            $tables = $this->showTables ();
        } else {
            if (is_array ( $tables )) {
                $tables = array_map ( function ( $item ) {
                    return $this->config[ $this->group ][ 'prefix' ] . $item;
                } , $tables );
            } else {
                $tables = explode ( ',' , $tables );
                $tables = array_map ( function ( $item ) {
                    return $this->config[ $this->group ][ 'prefix' ] . $item;
                } , $tables );
            }
        }

        $success = true;

        foreach ($tables as $table) {
            $success = ($this->pdo->exec ( "OPTIMIZE TABLE {$table}" ) === false);
        }

        return $success ? 'Optimize Tables Success' : $success;
    }

    /**
     * @return array|bool
     */
    public function showTables ()
    {
        $result = $this->pdo->query ( "SHOW TABLES" )->fetchAll ( PDO::FETCH_ASSOC );

        return array_map ( function ( $item ) {
            return array_values ( $item )[ 0 ];
        } , $result );
    }

    /**
     * @param $tables
     * @return Bool|String
     */
    public function repairTables ( $tables = '*' )
    {
        if ($this->table == '') {
            if (trim ( $tables ) == '*') {
                $tables = $this->showTables ();
            } else {
                if (is_array ( $tables )) {
                    $tables = array_map ( function ( $item ) {
                        return $this->config[ $this->group ][ 'prefix' ] . $item;
                    } , $tables );
                } else {
                    $tables = explode ( ',' , $tables );
                    $tables = array_map ( function ( $item ) {
                        return $this->config[ $this->group ][ 'prefix' ] . $item;
                    } , $tables );
                }
            }
        } else {
            $tables = array( $this->table );
        }

        $success = true;

        foreach ($tables as $table) {
            $success = $this->pdo->exec ( "REPAIR TABLE {$table}" ) === false ? false : true;
        }

        return $success ? 'Repair Tables Success' : $success;
    }

    /**
     * Drop Table or database
     * @return bool
     * @throws DatabaseException
     */
    public function drop ()
    {
        $drop = !is_null ( $this->table ) ? " TABLE " : $this->database ? " DATABASE " : "";

        $item = !is_null ( $this->table ) ? $this->table : $this->database ? " DATABASE " : "";

        $queryString = "DROP {$drop} IF EXISTS {$item}";

        try {
            return ($this->pdo->exec ( $queryString ) === false);
        } catch (\PDOException $e) {
            throw new DatabaseException
            (
              $e->getMessage()."<br />".
              "[QUERY: $queryString] "
            );
        }


    }

    /**
     * Truncate table
     * @return bool
     * @throws DatabaseException
     */
    public function truncate ()
    {
        $queryString = "TRUNCATE TABLE IF EXISTS {$this->table} ";

        try {
            return $this->pdo->exec ( $queryString ) === false ? false : true;
        } catch (\PDOException $e) {
            throw new DatabaseException
            (
              $e->getMessage()."<br />".
              "[QUERY: $queryString] "
            );
        }

    }

    /**
     * @return null|object
     * @throws DatabaseException
     */
    public function list_tables ()
    {
        $database = $this->database ?: $this->config[ $this->group ][ 'dbname' ];

        $queryString = "SHOW TABLES FROM {$database}";

        try {
            $result = $this->pdo->query ( $queryString );
            if ($result->rowCount () > 0) {
                return $result->fetchAll ();
            }
            return null;
        } catch (\PDOException $e) {
            throw new DatabaseException
            (
              $e->getMessage()."<br />".
              "[QUERY: $queryString] "
            );
        }

    }

    /**
     * @return array|bool
     * @throws DatabaseException
     */
    public function listColumns ()
    {
        $queryString = "SHOW COLUMNS FROM {$this->table}";
        try {
            $result = $this->pdo->query ( $queryString );
            if ($result->rowCount () > 0) {
                return $result->fetchAll ();
            }
            return null;
        } catch (\PDOException $e) {
            throw new DatabaseException
            (
              $e->getMessage()."<br />".
              "[QUERY: $queryString] "
            );
        }

    }


    /**
     * @return mixed
     */
    public function lastId ()
    {
      if(!is_null($this->pdo)) {
        return $this->pdo->lastInsertId ();
      }

    }

    /**
     * @param $data
     * @return string
     */
    public function escape ( $data )
    {
        if (is_array ( $data )) {
            foreach ($data as $key => $value) {
                $data[ $key ] = $this->pdo->quote ( trim ( $value ) );
            }
        } else {
            $data = $this->pdo->quote ( trim ( $data ) );
        }

        return $data;
    }


    public function reset ()
    {
        $this->execute_data = [];
        $this->select = [];
        $this->where = [];
        $this->limit = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->set = [];
        $this->join = [];
        $this->table = null;
        $this->database = null;
        $this->lastId = null;
        $this->exception = null;

        return $this;
    }

    public function __call ( $method , $args )
    {
        return $this->pdo->$method( ...$args );
    }

    public function __toString ()
    {
        if(!is_null($this->queryString)) {
            return $this->queryString;
        } else {
            return 'Database Library';
        }
    }


    public function __clone ()
    {
        $this->reset();

        return $this;
    }


}
