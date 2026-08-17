<?php

return [
    'driver'   => $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: 'pgsql',
    'host'     => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1',
    'port'     => $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '5432',
    'database' => $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'cliento',
    'username' => $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'tests',
    'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: 'othmane',
    'charset'  => 'utf8',
    
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,    
        PDO::ATTR_EMULATE_PREPARES   => false,              
    ],
];