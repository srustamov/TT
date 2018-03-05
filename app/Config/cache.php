<?php


return array(
  
    'driver' => 'file',



    'file' => array(
        'path' => path('storage/cache/data')
    ),

    /*--------------------------------------------------------
     | Database table create { Cache::createDatabaseTable() }
     |--------------------------------------------------------
    */
    'database' => array(
            'table'    => 'cache'
        ),

    'memcache' => array(
            'host' => '127.0.0.1',
            'port' => 11211
        ),

    'redis' => array(
            'host' => '127.0.0.1',
            'port' => 6379
        ),
);