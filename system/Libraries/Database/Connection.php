<?php namespace System\Libraries\Database;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Database/Connection
 */

use System\Exceptions\DatabaseException;
use System\Facades\Config;
use PDOException;
use PDO;

abstract class Connection
{
    protected $connections = [];

    protected $config = [];

    /**@var PDO*/
    protected $pdo;

    protected $group = 'default';


    public function __construct()
    {
        $this->reconnect();
    }


    public function reconnect()
    {
        if (!isset($this->connections[ $this->group ])) {
            $this->config[ $this->group ] = Config::get("database.$this->group");

            $config = $this->config[ $this->group ];

            try {
                $dsn = "host={$config['hostname']};dbname={$config['dbname']};charset={$config['charset']}";
                $this->connections[ $this->group ] = new PDO("mysql:{$dsn}", $config['username'], $config['password']);
                $this->pdo = $this->connections[ $this->group ];
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->query("SET CHARACTER SET  " . $config[ 'charset' ]);
                $this->pdo->query("SET NAMES " . $config[ 'charset' ]);
            } catch (PDOException $e) {
                throw new DatabaseException($e->getMessage());
            }
        } else {
            $this->pdo = $this->connections[ $this->group ];
        }
    }


    /**
     * @param string|null $query
     * @return mixed
     */
    public function pdo(string $query = null): PDO
    {
        if ($query !== null) {
            return $this->pdo->query($query);
        }
        return $this->pdo;
    }


    /**
     * @param string $group
     * @return $this
     * @throws DatabaseException
     */
    public function connect($group = 'default')
    {
        $this->group = $group;

        $this->reconnect();

        return $this;
    }


    /**
     * Database connection close;
     * @param string|null $group
     */
    public function disconnect(string $group = null)
    {
        $connect = $group? :$this->group;

        if (isset($this->connections[ $connect ])) {
            unset($this->connections[ $connect ]);
        }

        $this->pdo = null;
    }
}
