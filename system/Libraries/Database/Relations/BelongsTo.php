<?php


namespace System\Libraries\Database\Relations;


trait BelongsTo
{
    public function belongsTo(string $model,$foreing_key)
    {
        $object = new $model();

        return $object->where($object->getPrimaryKey(),$this[$foreing_key]);
    }
}

