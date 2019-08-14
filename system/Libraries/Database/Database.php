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
use System\Libraries\Arr;
use System\Exceptions\DatabaseException;

class Database extends Connection
{
    use Traits\InsertDeleteUpdate;
    use Traits\Join;
    use Traits\Where;
    use Traits\Calculation;

    private $table;

    private $select = [];

    private $where = [];

    private $limit = [];

    private $orderBy = [];

    private $groupBy = [];

    private $join = [];

    private $database;

    private $bindValues = [];





    /**
     * @return object|null
     * @param string $sql
     * @param array $data
     */

    public function raw(string $sql, array $data = [])
    {
        if (empty($data)) {
            $stmt = $this->pdo()->query($sql);
        } else {
            $stmt = $this->pdo()->prepare($sql);

            $stmt->execute($data);
        }

        return ($stmt->rowCount() > 0) ? $stmt : null;
    }


    /**
     * @return object|null
     * @throws DatabaseException
     */

    public function first()
    {
        return $this->get(true);
    }

    /**
     * @param bool $first
     * @param int $fetch
     * @return null|object
     * @throws DatabaseException
     */
    public function get($first = false, $fetch = PDO::FETCH_OBJ)
    {
        if (empty($this->limit)) {
            $query = $this->getQueryString() . ($first ? ' LIMIT 1' : '');
        } else {
            $query = $this->getQueryString();
        }

        $queryString = $this->normalizeQueryString($query);

        try {
            $statement = $this->pdo->prepare($query);

            $this->bindValues($statement);

            $statement->execute();

            $this->reset();

            if ($statement->rowCount() > 0) {
                return $first ? $statement->fetch($fetch) : $statement->fetchAll($fetch);
            } else {
                return null;
            }
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage(), $queryString);
        }
    }


    /**
     * @return string
     */
    private function getQueryString()
    {
        if (empty($this->select)) {
            $this->select[] = "*";
        }

        $query = "SELECT " . implode(',', $this->select) . " FROM " . $this->table . " ";

        $query .= implode(' ', array_merge($this->join, $this->where, $this->orderBy, $this->groupBy, $this->limit));

        return $query;
    }

    private function normalizeQueryString($query)
    {
        foreach ($this->bindValues as $value) {
            $position = strpos($query, '?');

            if ($position !== false) {
                $query = substr_replace($query, $value, $position, 1);
            }
        }
        return $query;
    }

    public function bindValues(PDOStatement $statement)
    {
        foreach ($this->bindValues as $key => $value) {
            $statement->bindValue(
                $key + 1,
                $value,
                is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
    }


    public function transaction(\Closure $callback)
    {
        $this->pdo->beginTransaction();

        call_user_func($callback, $this);
    }


    public function table(String $table)
    {
        $this->table = $this->config[$this->group]['prefix'] . $table;
        return $this;
    }

    public function database(String $database)
    {
        $this->database = $database;
        return $this;
    }

    public function toArray($first = false)
    {
        return $this->get($first, PDO::FETCH_ASSOC);
    }

    public function toJson($first = false)
    {
        if (($result = $this->toArray($first))) {
            return json_encode($result);
        } else {
            return null;
        }
    }

    public function select($select)
    {
        if (is_array($select)) {
            $select = implode(',', $select);
        }

        $this->select = [$select];

        return $this;
    }

    public function limit($limit, $offset = 0)
    {
        $this->limit[] = ' LIMIT ' . $offset . ',' . $limit;
        return $this;
    }

    public function orderBy($column, $sort = 'ASC')
    {
        $this->orderBy[] = " ORDER BY " . $column . " " . strtoupper($sort);
        return $this;
    }

    public function orderByRand()
    {
        $this->orderBy[] = " ORDER BY RAND() ";
        return $this;
    }

    public function groupBy($column)
    {
        $this->groupBy[] = ' GROUP BY ' . $column;
        return $this;
    }


    private function normalizeCrud($data)
    {
        return implode(',', array_map(function ($item) {
            return $item . "=?";
        }, array_keys($data)));
    }


    /**
     * @param string $tables
     * @return bool
     * @internal param $table
     */
    public function optimizeTables($tables = '*')
    {
        if (trim($tables) == '*') {
            $tables = $this->showTables();
        } else {
            if (is_array($tables)) {
                $tables = array_map(function ($item) {
                    return $this->config[$this->group]['prefix'] . $item;
                }, $tables);
            } else {
                $tables = explode(',', $tables);

                $tables = array_map(function ($item) {
                    return $this->config[$this->group]['prefix'] . $item;
                }, $tables);
            }
        }

        $success = true;

        foreach ($tables as $table) {
            $success = ($this->pdo->exec("OPTIMIZE TABLE {$table}") === false);
        }

        return $success;
    }

    /**
     * @return array|bool
     */
    public function showTables()
    {
        $result = $this->pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($item) {
            return array_values($item)[0];
        }, $result);
    }

    /**
     * @param $tables
     * @return Bool
     */
    public function repairTables($tables = '*')
    {
        if ($this->table == '') {
            if (trim($tables) == '*') {
                $tables = $this->showTables();
            } else {
                if (is_array($tables)) {
                    $tables = array_map(function ($item) {
                        return $this->config[$this->group]['prefix'] . $item;
                    }, $tables);
                } else {
                    $tables = explode(',', $tables);

                    $tables = array_map(function ($item) {
                        return $this->config[$this->group]['prefix'] . $item;
                    }, $tables);
                }
            }
        } else {
            $tables = array($this->table);
        }

        $success = true;

        foreach ($tables as $table) {
            $success = $this->pdo->exec("REPAIR TABLE {$table}") === false ? false : true;
        }

        return $success;
    }

    /**
     * Drop Table or database
     * @return bool
     * @throws DatabaseException
     */
    public function drop()
    {
        $drop = !is_null($this->table) ? " TABLE " : $this->database ? " DATABASE " : "";

        $item = !is_null($this->table) ? $this->table : $this->database ? " DATABASE " : "";

        $queryString = "DROP {$drop} IF EXISTS {$item}";

        try {
            return ($this->pdo->exec($queryString) === false);
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage(), $queryString);
        }
    }

    /**
     * Truncate table
     * @return bool
     * @throws DatabaseException
     */
    public function truncate()
    {
        $queryString = "TRUNCATE TABLE IF EXISTS {$this->table} ";

        try {
            return ($this->pdo->exec($queryString) === false);
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage(), $queryString);
        }
    }

    /**
     * @return null|object
     * @throws DatabaseException
     */
    public function list_tables()
    {
        $database = $this->database ?: $this->config[$this->group]['dbname'];

        $queryString = "SHOW TABLES FROM {$database}";

        try {
            $result = $this->pdo->query($queryString);

            if ($result->rowCount() > 0) {
                return $result->fetchAll();
            }
            return null;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage(), $queryString);
        }
    }

    /**
     * @return array|bool
     * @throws DatabaseException
     */
    public function listColumns()
    {
        $queryString = "SHOW COLUMNS FROM {$this->table}";

        try {
            $result = $this->pdo->query($queryString);

            if ($result->rowCount() > 0) {
                return $result->fetchAll();
            }
            return null;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage(), $queryString);
        }
    }


    /**
     * @return mixed
     */
    public function lastId()
    {
        if (!is_null($this->pdo)) {
            return $this->pdo->lastInsertId();
        }
    }

    /**
     * @param $data
     * @return string
     */
    public function escape($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->pdo->quote(trim($value));
            }
        } else {
            $data = $this->pdo->quote(trim($data));
        }

        return $data;
    }


    public function reset()
    {
        $this->bindValues = [];
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

    public function __call($method, $args)
    {
        return $this->pdo->$method(...$args);
    }


    public function __toString()
    {
        if (!is_null($this->queryString)) {
            return $this->queryString;
        } else {
            return 'Database Library';
        }
    }


    public function __clone()
    {
        $this->reset();

        return clone $this;
    }
}
