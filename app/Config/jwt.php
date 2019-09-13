<?php

return [
    'issued' => setting('APP_URL','http://localhost'),
    'expires' => 3600*5,
    'key' => setting('JWT_SECRET','secret-key'),
    'claims' => [

    ]
];
