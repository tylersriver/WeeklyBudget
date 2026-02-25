<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

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
    $handler = static function () use ($app): void {
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
