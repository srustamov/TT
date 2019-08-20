<?php namespace System\Libraries\Database\Relations;



trait HasOne{


    public function hasOne($model,$foreign_key)
    {
      return (new $model())->find([$foreign_key => $this[self::getInstance()->getPrimaryKey()]]);
    }


}
