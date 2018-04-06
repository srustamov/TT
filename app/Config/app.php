<?php

return array(


    /*
    |--------------------------------------------------------------------------
    | Application environment mode; true -> DEVELOPMENT | false -> PRODUCTION
    |--------------------------------------------------------------------------
    |
    */


    'debug'  => setting('APP_DEBUG',false),


    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    */

    'locale' => setting('LOCALE','en'),


    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    */

    'app_name' => setting('APP_NAME','TT'),




    /*
    |--------------------------------------------------------------------------
    | Application Base Url
    |--------------------------------------------------------------------------
    |
    */

    'base_url' => setting('URL','http://localhost:8000/'),



    /*
    |--------------------------------------------------------------------------
    | Application Encryption Key
    |--------------------------------------------------------------------------
    |
    */

    'encryption_key'  => setting('APP_KEY'),


    /*
    |--------------------------------------------------------------------------
    | Application classes
    |--------------------------------------------------------------------------
    |
    */

    'classes'  => array(
        'array' => 'System\Libraries\Arr',
        'authentication' => 'System\Libraries\Auth\Authentication',
        'cache' => 'System\Libraries\Cache\Cache',
        'console' => 'System\Engine\Cli\Console',
        'cookie' => 'System\Libraries\Cookie',
        'database' => 'System\Libraries\Database\Database',
        'email' => 'System\Libraries\Mail\Email',
        'file' => 'System\Libraries\File',
        'hash' => 'System\Libraries\Hash',
        'html' => 'System\Libraries\Html',
        'http' => 'System\Libraries\Http',
        'input' => 'System\Libraries\Input',
        'lang' => 'System\Libraries\Language',
        'language' => 'System\Libraries\Language',
        'load' => 'System\Core\Load',
        'middleware' => 'System\Engine\Http\Middleware',
        'openssl' => 'System\Libraries\Encrypt\OpenSsl',
        'redirect' => 'System\Libraries\Redirect',
        'redis' => 'System\Libraries\RedisFactory',
        'request' => 'System\Engine\Http\Request',
        'response' => 'System\Engine\Http\Response',
        'router' => 'System\Engine\Http\Router',
        'session' => 'System\Libraries\Session\Session',
        'str' => 'System\Libraries\Str',
        'string' => 'System\Libraries\Str',
        'storage' => 'System\Libraries\Storage',
        'url' => 'System\Libraries\Url',
        'validator' => 'System\Libraries\Validator',
        'view' => 'System\Libraries\View\View',
    ),


);
