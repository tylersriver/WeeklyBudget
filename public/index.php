<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Cycle\Database\DatabaseManager;
use Slim\Factory\AppFactory;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Boot phase — runs once in worker mode
$container = require __DIR__ . '/../config/container.php';
AppFactory::setContainer($container);
$app = AppFactory::create();

(require __DIR__ . '/../config/middleware.php')($app);
(require __DIR__ . '/../config/routes.php')($app);

// FrankenPHP worker mode
if (function_exists('frankenphp_handle_request')) {
    $dbal = $container->get(DatabaseManager::class);

    $handler = static function () use ($app, $dbal): void {
        // Reconnect stale MySQL connections dropped during idle periods
        try {
            $dbal->database()->query('SELECT 1')->fetch();
        } catch (\Throwable) {
            $dbal->database()->getDriver()->disconnect();
            $dbal->database()->getDriver()->connect();
        }

        $app->run();
        gc_collect_cycles();
    };

    do {
        $running = \frankenphp_handle_request($handler);
    } while ($running);
} else {
    // Standard PHP-FPM / CLI server fallback
    $app->run();
}
