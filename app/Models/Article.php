<?php  namespace App\Models;


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link    https://github.com/srustamov/TT
 */

use TT\Libraries\Database\Model;


class Article extends Model
{

   // default Articles
   //protected $table;

   // default [*]
   //protected $select;

   /*
   * default Primary Key id
   protected $primaryKey;
   */



   public function user()
   {
       return $this->belongsTo(User::class,'user_id');
   }

}
