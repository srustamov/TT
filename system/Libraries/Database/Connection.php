<?php namespace System\Libraries\Database;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Database/Connection
 */

use System\Exceptions\DatabaseException;
use System\Facades\Load;
use PDOException;
use PDO;



abstract class Connection
{

    protected $general = [];

    protected $config = [];

    protected $pdo;

    protected $group = 'default';


    function __construct ()
    {
        $this->reconnect ();
    }


    public function reconnect ()
    {
        if (!isset( $this->general[ $this->group ] ))
        {

            $this->config[ $this->group ] = Load::config ( "database.$this->group" );

            $config = $this->config[ $this->group ];

            try
            {
                $dsn = "host={$config[ 'hostname' ]};dbname={$config[ 'dbname' ]};charset={$config[ 'charset' ]}";
                $this->general[ $this->group ] = new PDO( "mysql:{$dsn}" , $config[ 'username' ] , $config[ 'password' ] );
                $this->pdo = $this->general[ $this->group ];
                $this->pdo->setAttribute ( PDO::ATTR_DEFAULT_FETCH_MODE , PDO::FETCH_OBJ );
                $this->pdo->setAttribute ( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
                $this->pdo->query ( "SET CHARACTER SET  " . $config[ 'charset' ] );
                $this->pdo->query ( "SET NAMES " . $config[ 'charset' ] );
            }
            catch (PDOException $e)
            {
                throw new DatabaseException( $e->getMessage() );
            }
        }
        else
        {
            $this->pdo = $this->general[ $this->group ];
        }
    }


    public function pdo (String $query = null)
    {
        if(!is_null($query))
        {
          return $this->pdo->query($query);
        }
        else
        {
          return $this->pdo;
        }
    }


    public function connect ( $group = 'default' )
    {
        $this->group = $group;

        $this->reconnect ();

        return $this;
    }


    /**
     * Database connection close;
     */
    public function close ()
    {
        if (isset( $this->general[ $this->group ] ))
        {
            unset( $this->general[ $this->group ] );
        }

        $this->pdo = null;

    }


}
