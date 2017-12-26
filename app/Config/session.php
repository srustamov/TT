<?php

/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/SamirRustamov/TT
 */





return array(

    /*
    |---------------------------------------------------------
    | Session Driver
    |---------------------------------------------------------
    |
    | Driver use "database" or "file"
    |
    | Important! if using "database" run "php manage session:table --create yourTableName" command on command line
    | Or run code: Console::command("session:table --create yourTableName");
    |
    */
    'driver'          => setting('SESSION_DRIVER','file'),


    /*
    |---------------------------------------------------------
    | Session Files Location
    |---------------------------------------------------------
    */
    'files_location'   => storage_dir('sessions'),



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
    | Cookies
    |--------------------------------------------------------------------------
    */

    'only_cookies' => true,



    'cookie' => array(

        'name' => 'TT_SESSION',

        'path' => '/',

        'secure' => false,

        'domain' => null,

        'http_only' => true,

     ),





);
