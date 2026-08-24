<?php

return [
    'driver' => getenv('DB_DRIVER') ?: 'sqlite',
    
    'sqlite' => [
        'path' => __DIR__ . '/../storage/app-db.sqlite'
    ],
    
    'mysql' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'database' => getenv('DB_NAME') ?: 'joonweb_app',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: ''
    ]
];
