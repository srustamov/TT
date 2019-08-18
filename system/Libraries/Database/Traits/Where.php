<?php namespace System\Libraries\Database\Traits;

/**
 * @package    TT
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link https://github.com/srustamov/TT
 * @subpackage    Library
 * @category    Database
 */

use System\Libraries\Arr;

trait Where
{
    public function where($column, $value = false, $mark = null, $logic = ["WHERE", "AND"])
    {
        if ($mark !== null) {
            $this->where[] = (empty($this->where) ? $logic[0] : $logic[1]) . " {$column} {$value} ? ";

            $this->bindValues[] = $mark;
        } elseif ($value === false) {
            if (is_array($column) && Arr::isAssoc($column)) {
                foreach ($column as $key => $value) {
                    $this->where($key, $value);
                }
            } else {
                $this->where[] = (empty($this->where) ? $logic[0] : $logic[1]) . " " . $column . " ";
            }
        } else {
            $this->where[] = (empty($this->where) ? $logic[0] : $logic[1]) . " " . $column . " = ? ";
            $this->bindValues[] = $value;
        }

        return $this;
    }


    public function orWhere($column, $value = false, $mark = null)
    {
        return $this->where($column, $value, $mark, ["WHERE", "OR"]);
    }


    public function notWhere($column, $value = false, $mark = null)
    {
        return $this->where($column, $value, $mark, ["WHERE NOT", "AND NOT"]);
    }

    public function orNotWhere($column, $value = false, $mark = null)
    {
        return $this->where($column, $value, $mark, ["WHERE NOT", "OR NOT"]);
    }

    public function whereNotIn($column, $in)
    {
        return $this->whereIn($column, $in, "NOT");
    }

    public function whereIn($column, $in, $logic = "")
    {
        $in = is_array($in) ? $in : explode(',', $in);

        $this->where[] = (empty($this->where) ? "WHERE " : " AND ") . $column . " {$logic} IN(" . rtrim(str_repeat('?,', count($in)), ',') . ")";

        $this->bindValues = array_merge($this->bindValues, $in);

        return $this;
    }

    public function orWhereNull($column)
    {
        return $this->whereNull($column, "OR");
    }

    public function whereNull($column, $logic = "AND")
    {
        $this->where[] = (!empty($this->where) ? $logic : "WHERE") . " {$column} IS NULL ";
        return $this;
    }

    public function orWhereNotNull($column)
    {
        return $this->WhereNotNull($column, "OR");
    }

    public function whereNotNull($column, $logic = "AND")
    {
        $this->where[] = (!empty($this->where) ? $logic : "WHERE") . " {$column} IS NOT NULL ";
        return $this;
    }

    public function between($where, $start, $stop, $mark = 'AND')
    {
        $this->where[] = empty($this->where) ? "WHERE " : "AND " . $where . " BETWEEN ? {$mark} ? ";

        $this->bindValues = array_merge($this->bindValues, [$start, $stop]);

        return $this;
    }


    public function notLike($column, $like)
    {
        return $this->like($column, $like, "NOT");
    }

    public function like($column, $like, $logic = "")
    {
        $this->where[] = (empty($this->where) ? "WHERE " : "AND ") . "{$column} {$logic} LIKE ? ";

        $this->bindValues[] = $like;

        return $this;
    }
}
