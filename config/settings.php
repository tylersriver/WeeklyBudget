<?php

declare(strict_types=1);

return [
    'app' => [
        'env' => $_ENV['APP_ENV'] ?? 'development',
    ],
    'db' => [
        'path' => $_ENV['DB_PATH'] ?? __DIR__ . '/../var/data/weeklybudget.sqlite',
    ],
    'twig' => [
        'path'  => __DIR__ . '/../templates',
        'cache' => ($_ENV['APP_ENV'] ?? 'development') === 'production'
            ? __DIR__ . '/../var/cache'
            : false,
    ],
];
