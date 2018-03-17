<?php namespace System\Core;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */
//-------------------------------------------------------------



//------------------------------------------------------
// Model Class
//------------------------------------------------------


use System\Facades\DB;


abstract class Model
{


    private static $instance;

    private static $data;

    protected $table;

    protected $primaryKey = 'id';

    protected $fillable   = ['*'];




    function __construct()
    {
        if (is_null($this->table))
        {
            $called_class = explode('\\', get_called_class());
            $this->table  = strtolower(array_pop($called_class)).'s';
        }
    }



    public function __set($column,$value)
    {
      self::$data[$column] = $value;
    }




    /**
     * @return bool
     */
    public function save():Bool
    {
        if (!is_null(self::$data))
        {
            $return = static::create(self::$data);

            self::$data = null;

            return $return;
        }
        return false;
    }


    /**
     * @param array $data
     * @return bool
     */
    public static function create($data = []):Bool
    {
        return DB::table(static::getModelInstance()->table)->insert($data);
    }


    /**
     * @return mixed
     * @internal param $primaryKey
     */
    public static function find()
    {
        $find  = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

        $first = (count($in) == 1);

        return DB::table(static::getModelInstance()->table)
                   ->whereIn(static::getModelInstance()->primaryKey,$find)
                   ->get($first);
    }


    /**
     * @param $select
     * @return mixed
     */
    public static function all($select = null)
    {
        $select = !empty(func_get_args()) ? func_get_args() : static::getModelInstance()->fillable;

        $result =  DB::table(static::getModelInstance()->table)->select($select)->get();

        return $result;
    }




    public function setPrimaryKey($key)
    {
      $this->primaryKey = $key;
      return $this;
    }


    public function setTable($table)
    {
      $this->table = $table;
      return $this;
    }


    public function _call($name, $arguments)
    {
        return DB::table($this->table)->select($this->fillable)->{$name}(...$arguments);
    }



    public static function __callStatic($name, $arguments)
    {
        return static::getModelInstance()->_call($name, $arguments);
    }


    public static function getModelInstance()
    {
        if(is_null(self::$instance))
        {
            self::$instance = new static;
        }

        return self::$instance;
    }


}
