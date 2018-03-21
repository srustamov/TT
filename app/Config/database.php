<?php

return array(

    'default' => array(
        "hostname"  => setting('DB_HOST'),

        "username"  => setting('DB_USER'),

        "password"  => setting('DB_PASS'),

        "dbname"    => setting('DB_NAME'),

        'prefix'    => setting('DB_PREFIX'),

        "charset"   => setting('DB_CHARSET'),

    ),

    /*
    'connect2' => array(  // DB::connect('connect2')->...;
          "hostname"  => 'localhost',

          "username"  => 'root',

          "password"  => '',

          "dbname"    => 'auth',

          'prefix'    => '',

          "charset"   => 'utf8',

     ),
     */



);
