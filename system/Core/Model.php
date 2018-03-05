<?php namespace System\Core;

//-------------------------------------------------------------
/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/SamirRustamov/TT
 */
//-------------------------------------------------------------



//------------------------------------------------------
// Model Class
//------------------------------------------------------


use System\Libraries\Database\Database;


abstract class Model
{

    private static $data;

    protected $table;

    protected $primaryKey = 'id';

    protected $fillable   = ['*'];




    function __construct()
    {
        if (is_null($this->table))
        {
            $called_class = explode('\\', get_called_class());
            $this->table  = mb_strtolower(array_pop($called_class),"UTF-8").'s';
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
            $return = self::create(self::$data);
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
        return (new Database())->table(( new static )->table)->set($data)->insert();
    }


    /**
     * @return mixed
     * @internal param $primaryKey
     */
    public static function find()
    {
        if(is_array(func_num_args()[0]))
        {
          $in = func_num_args()[0];
        }
        else
        {
          $in = func_num_args();
        }
        return (new Database())->table(( new static )->table)->whereIn((new static)->primaryKey,$in)->get();
    }


    /**
     * @param $select
     * @return mixed
     */
    public static function all($select = null)
    {
        $select = !empty(func_get_args()) ? func_get_args() : (new static)->fillable;

        $result =  (new Database())->table((new static)->table)->select($select)->get();

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


    private function _call($name, $arguments)
    {
        return (new Database())->table($this->table)->select($this->fillable)->{$name}(...$arguments);
    }


    public function __call($name, $arguments)
    {
       return $this->_call($name, $arguments);
    }


    public static function __callStatic($name, $arguments)
    {
        return (new static())->_call($name, $arguments);
    }


}
