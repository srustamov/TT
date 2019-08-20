<?php namespace System\Libraries\Database;

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */


//------------------------------------------------------
// Model Class
//------------------------------------------------------

use RuntimeException;
use System\Facades\DB;
use System\Libraries\Arr;
use ArrayAccess, JsonSerializable;
use App\Exceptions\ModelNotFoundException;


abstract class Model implements ArrayAccess, JsonSerializable
{
    use Relations\HasMany,Relations\HasOne,Relations\BelongsTo;

    protected static $models = [];

    protected $attributes = [];

    protected $table;

    protected $select = ['*'];

    protected $primaryKey = 'id';


    public function __construct()
    {
        if (!isset(self::$models[static::class])) {
            $this->boot();
        }
    }


    protected function boot()
    {
        $model = $this;

        if ($model->table === null) {
            $called_class = explode('\\', static::class);

            $this->table = strtolower(array_pop($called_class)) . 's';
        }
        if ($model->primaryKey === null) {
            $model->primaryKey = 'id';
        }

        self::$models[static::class] = $model;

    }


    /**
     * @return bool
     */
    public function save(): Bool
    {
        if (!empty(self::getAttributes())) {
            $pk = self::getInstance()->primaryKey;
            if (array_key_exists($pk, self::getAttributes())) {
                /**@var $query Database */
                $query = DB::table(self::getTable());

                $query->where($pk, self::getAttributes()[$pk]);

                $return = $query->update(Arr::except(self::getAttributes(), [$pk]));
            } else {
                $return = static::create(self::getAttributes());
            }

            self::setAttributes([]);

            return $return;
        }
        return false;
    }


    /**
     * @param array $data
     * @return bool
     */
    public static function create(array $data): Bool
    {
        return DB::table(self::getTable())->insert($data);
    }


    /**
     * @param array|int $primaryKey
     * @return mixed
     * @internal param $pk
     */
    public static function find($primaryKey)
    {
        $pk = self::getInstance()->getPrimaryKey();

        if ($pk === null) {
            throw new RuntimeException('No primary key defined on model.');
        }

        if (is_array($primaryKey) && Arr::isAssoc($primaryKey)) {
            $where = $primaryKey;
        } else {
            $where = [$pk => $primaryKey];
        }
        $select = self::getInstance()->select ?? '*';

        /**@var $query Database */
        $query = DB::table(self::getTable());

        $result = $query->select($select)->where($where)->toArray(true);

        if (!$result) {
            return null;
        }

        self::getInstance()->setAttributes((array)$result);

        return self::getInstance();

    }


    public static function findOrFail(...$args)
    {
        if ($model = self::getInstance()->find(...$args)) {
            return $model;
        }
        throw new ModelNotFoundException;
    }


    public static function destroy($primaryKey)
    {
        $pk = self::getInstance()->getPrimaryKey();

        if ($pk === null) {
            throw new RuntimeException('No primary key defined on model.');
        }

        /**@var $query Database */
        $query = DB::table(self::getTable());

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
        $select = $select ?? self::getInstance()->select ?? '*';

        return DB::table(self::getTable())->select($select)->get();
    }


    public function setPrimaryKey($key)
    {
        self::getInstance()->primaryKey = $key;

        return self::getInstance();
    }

    public function getPrimaryKey()
    {
        return self::getInstance()->primaryKey;
    }


    public static function getTable()
    {
        return self::getInstance()->table;
    }


    protected function setTable($table)
    {
        self::getInstance()->table = $table;

        return self::getInstance();
    }

    public static function setAttributes(array $attributes)
    {
        self::getInstance()->attributes = $attributes;

        return self::getInstance();
    }


    public static function getAttributes(): array
    {
        return self::getInstance()->attributes;
    }

    public static function getInstance()
    {
        return self::$models[static::class] ?? new static();
    }

    private function callCustomMethod($name, $arguments)
    {
        $select = self::getInstance()->select ?? '*';

        return DB::table(self::getTable())->select($select)->{$name}(...$arguments);
    }


    public function __call($name, $arguments)
    {
        return $this->callCustomMethod($name, $arguments);
    }


    public static function __callStatic($name, $arguments)
    {
        return (new static)->callCustomMethod($name, $arguments);
    }


    /**
     * @param $column
     * @param $value
     */
    public function __set($column, $value)
    {
        self::getInstance()->attributes[$column] = $value;
    }

    public function __isset($name)
    {
        return array_key_exists($name, self::getAttributes());
    }

    /**
     * @param $column
     * @return array|null
     */
    public function __get($column)
    {
        return self::getInstance()->attributes[$column] ?? null;
    }


    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return self::getAttributes()[$offset] ?? null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        self::getInstance()->attributes[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if (array_key_exists($offset, self::getAttributes())) {
            unset(self::getInstance()->attributes[$offset]);
        }
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, self::getAttributes());
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return array The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function jsonSerialize(): array
    {
        return self::getAttributes();
    }
}
