<?php

return[

    'table' => 'users',
    // Attempt drivers [session,database,redis]
    'attemptDriver' => 'session',
    'maxAttempts' => 5,
    'lockTime' => 300, //seconds
    'hidden' => [
        'password',
        'remember_token',
    ]
   
];
