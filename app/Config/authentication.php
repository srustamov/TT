<?php

return[
    'guards' => [
        'default' => [
            'model' => App\Models\User::class,
            'password_name' => 'password',
            'hidden' => [
                'password',
                'remember_token',
            ],
            'throttle' => [
                'enable' => false,
                'driver' => 'session', //drivers [session,database,redis]
                'max_attempts' => 5,
                'lock_time' => 300, //seconds
            ]
        ],

        'admin' => [
            'model' => App\Models\Admin::class,
            'password_name' => 'password',
            'hidden' => [
                'password',
                'remember_token',
            ],
            'throttle' => [
                'enable' => true,
                'driver' => 'session',
                'max_attempts' => 3,
                'lock_time' => 300, //seconds
            ]
        ]

    ]

];
