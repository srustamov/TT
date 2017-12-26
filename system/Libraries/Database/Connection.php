<?php namespace System\Libraries\Database;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/SamirRustamov/TT
 * @subpackage    Library
 * @category    Database/Connection
 */


use PDO;

use PDOException;

abstract class Connection
{

    protected static $general = [];

    protected static $config  = [];

    protected static $connect;

    protected $connection_group = 'default';



    function __construct()
    {
      $this->reconnect();
    }



    public function pdo ()
    {
        return self::$connect;
    }



    public  function reconnect()
    {
      if (!isset(static::$general[$this->connection_group]))
      {
          static::$config[$this->connection_group] = config("database")[$this->connection_group];

          $config    = static::$config[$this->connection_group];

          try
          {
              $dsn = "host={$config[ 'hostname' ]};dbname={$config[ 'dbname' ]};charset={$config[ 'charset' ]}";
              self::$general[$this->connection_group] = new PDO("mysql:{$dsn}" ,$config[ 'username' ] ,$config[ 'password' ]);
              self::$connect = static::$general[$this->connection_group];
              self::$connect->setAttribute (PDO::ATTR_DEFAULT_FETCH_MODE , PDO::FETCH_OBJ );
              self::$connect->setAttribute (PDO::ATTR_ERRMODE ,PDO::ERRMODE_EXCEPTION );
              self::$connect->query ( "SET CHARACTER SET  " . $config[ 'charset' ] );
              self::$connect->query ( "SET NAMES " . $config[ 'charset' ] );
          }
          catch (PDOException $e)
          {
             show_error($e);
          }
      }
      else
      {
        static::$connect = static::$general[$this->connection_group];
      }
    }




    public function connect ($connection_group = 'default')
    {
         $this->connection_group = $connection_group;
         $this->reconnect();
         return $this;
    }


    /**
    * Database connection close;
    */
    public function close ()
    {
        if (isset(self::$general[$this->connection_group]))
        {
            unset(self::$general[$this->connection_group]);

            $this->connection_group = 'default';

            $this->reconnect();
        }

    }



}
