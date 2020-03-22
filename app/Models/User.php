<?php

namespace App\Models;

use TT\Database\Orm\Model;


class User extends Model
{

    public function articles()
    {
        return $this->hasMany(Article::class, 'user_id');
    }
}
