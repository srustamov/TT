<?php


/**
 * @author  Samir Rustamov <rustemovv96@gmail.com>
 * @link 	https://github.com/srustamov/TT
 */



return [

  "guards" => [

        'user' => [
          'table' => 'users',
          'attempts_driver' => 'session', // or database
          'max_attempts' => 5,
          'lock_time' => 300, //seconds
          'hidden' => [
            'password',
            'remember_token'
          ]
        ],

        'admin' => [
          'table' => 'admins',
          'attempts_driver' => 'database',
          'max_attempts' => 3,
          'lock_time' => 60*30, //seconds
          'hidden' => [
            'password',
            'remember_token'
          ]
        ]
  ],


];
