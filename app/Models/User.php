<?php namespace App\Models;

use TT\Libraries\Database\Model;


class User extends Model
{

    public function articles()
    {
        return $this->hasMany(Article::class, 'user_id');
    }

    public function article()
    {
        return $this->hasOne(Article::class, 'user_id');
    }

}
