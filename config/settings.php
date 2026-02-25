<?php

declare(strict_types=1);

return [
    'app' => [
        'env' => $_ENV['APP_ENV'] ?? 'development',
    ],
    'db' => [
        'host'     => $_ENV['DB_HOST'] ?? 'localhost',
        'port'     => (int) ($_ENV['DB_PORT'] ?? 3306),
        'database' => $_ENV['DB_NAME'] ?? 'WeeklyBudget',
        'user'     => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASS'] ?? '',
    ],
    'twig' => [
        'path'  => __DIR__ . '/../templates',
        'cache' => ($_ENV['APP_ENV'] ?? 'development') === 'production'
            ? __DIR__ . '/../var/cache'
            : false,
    ],
];
