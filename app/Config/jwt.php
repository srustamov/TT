<?php

return [
    'issued' => setting('APP_URL','http://localhost'),
    'expires' => 3600*5,
    'key' => setting('API_KEY','secret-key'),
    'claims' => [

    ]
];
