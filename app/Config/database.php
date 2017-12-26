<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 		https://github.com/SamirRustamov/TT
 */

return array(

          'default' => array(
                "hostname"  => setting('DB_HOST'),

                "username"  => setting('DB_USER'),

                "password"  => setting('DB_PASS'),

                "dbname"    => setting('DB_NAME'),

                'prefix'    => setting('DB_PREFIX'),

                "charset"   => setting('DB_CHARSET'),

                "fetch_mod" => setting('DB_FETCHMOD'),
          ),

          /*
          'connect2' => array(  // DB::connect('connect2')->...;
                "hostname"  => 'localhost',

                "username"  => 'root',

                "password"  => 'samir96',

                "dbname"    => 'auth',

                'prefix'    => '',

                "charset"   => 'utf8',

                "fetch_mod" => 'array',
           ),
           */



);
