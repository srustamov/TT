<?php

return array(

    /*
    |---------------------------------------------------------
    | Session Driver
    |---------------------------------------------------------
    |
    | Driver use database|file|redis
    |
    | Important! if using "database" run "php manage session:table --create [Your-Table-Name]" command on command line
    | Or run code: Console::command("session:table --create yourTableName");
    |
    */
    'driver'          => setting('SESSION_DRIVER'),


    /*
    |---------------------------------------------------------
    | Session Files Location
    |---------------------------------------------------------
    */
    'files_location'   => path('sessions','storage'),



    /*
    |---------------------------------------------------------
    |  Session Database Table
    |---------------------------------------------------------
    */

    'table'          => 'sessions',



    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    */

    'lifetime'        => 3600,


    /*
    |--------------------------------------------------------------------------
    | Session Regenerate id
    |--------------------------------------------------------------------------
    |
    */

    'regenerate'        => false,


    /*
    |--------------------------------------------------------------------------
    | Cookies
    |--------------------------------------------------------------------------
    */

    'only_cookies' => true,



    'cookie' => array(

        'name' => 'tt_session',

        'path' => '/',

        'secure' => false,

        'domain' => null,

        'http_only' => true,

    ),





);
