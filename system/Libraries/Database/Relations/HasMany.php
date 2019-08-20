<?php namespace System\Libraries\Database\Relations;



trait HasMany
{


    public function hasMany($model, $foreign_key)
    {
        return (new $model)->where($foreign_key, $this[$this->primaryKey]);
    }





}
