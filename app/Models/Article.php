<?php

namespace App\Models;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use TT\Database\Orm\Model;


class Article extends Model
{

    // default articles
    //protected $table;

    /*
   * default Primary Key id
   protected $primaryKey;
   */



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
