<?php namespace System\Libraries\Database;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */




//------------------------------------------------------
// Model Class
//------------------------------------------------------

use App\Exceptions\ModelNotFoundException;
use System\Libraries\Arr;
use System\Facades\DB;

abstract class Model
{
    private static $attributes = [];

    protected $table;

    protected $select  = ['*'];

    protected $primaryKey = 'id';


    /**
     * @param $column
     * @param $value
     */
    public function __set($column, $value)
    {
        self::$attributes[$column] = $value;
    }




    /**
     * @return bool
     */
    public function save():Bool
    {
        if (!empty(self::$attributes)) {
            $return = static::create(self::$attributes);

            self::$attributes = [];

            return $return;
        }
        return false;
    }


    /**
     * @param array $data
     * @return bool
     */
    public static function create(array $data):Bool
    {
        return DB::table((new static)->getTable())->insert($data);
    }


    /**
     * @return mixed
     * @param array|int $primaryKey
     * @internal param $pk
     */
    public static function find($primaryKey)
    {
        $pk = (new static)->primaryKey;

        if (is_null($pk)) {
            throw new Exception('No primary key defined on model.');
        }

        $select = !is_null((new static)->select) ? (new static)->select : '*';

        $query =  DB::table((new static)->getTable())->select($select);

        $first = true;

        if (is_array($primaryKey)) {
            $query->whereIn($pk, $primaryKey);
            if (count($primaryKey) > 1) {
                $first = false;
            }
        } else {
            $query->where($pk, $primaryKey);
        }

        return $query->get($first);
    }


    public static function findOrFail(...$args)
    {
        if ($result = self::find(...$args)) {
            return $result;
        }
        throw new ModelNotFoundException;
    }


    public static function destroy($primaryKey)
    {
        $pk = (new static)->primaryKey;

        if (is_null($pk)) {
            throw new Exception('No primary key defined on model.');
        }

        $query = DB::table((new static)->getTable());

        if (is_array($primaryKey)) {
            $query->whereIn($pk, $primaryKey);
        } else {
            $query->where($pk, $primaryKey);
        }

        return $query->delete();
    }


    /**
     * @param $select
     * @return mixed
     */
    public static function all($select = null)
    {
        $select = !is_null((new static)->select) ? (new static)->select : '*';

        return DB::table((new static)->getTable())->select($select)->get();
    }




    public function setPrimaryKey($key)
    {
        $this->primaryKey = $key;

        return $this;
    }


    public function getTable()
    {
        if (is_null($this->table)) {
            $called_class = explode('\\', get_called_class());

            $this->table  = strtolower(array_pop($called_class)).'s';
        }

        return $this->table;
    }


    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }


    private function callCustomMethod($name, $arguments)
    {
        $select = !is_null($this->select) ? $this->select : '*';

        return DB::table($this->getTable())->select($select)->{$name}(...$arguments);
    }


    public function __call($name, $arguments)
    {
        return $this->callCustomMethod($name, $arguments);
    }



    public static function __callStatic($name, $arguments)
    {
        return (new static)->callCustomMethod($name, $arguments);
    }
}
