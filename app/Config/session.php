<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/srustamov/TT
 */





return array(

    /*
    |---------------------------------------------------------
    | Session Driver
    |---------------------------------------------------------
    |
    | Driver use "database" or "file"
    |
    | Important! if using "database" run "php manage session:table --create [Your-Table-Name]" command on command line
    | Or run code: Console::command("session:table --create yourTableName");
    |
    */
    'driver'          => setting('SESSION_DRIVER','file'),


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
