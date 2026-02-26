<?php

declare(strict_types=1);

use App\Auth\Infrastructure\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app): void {
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();

    $twig = $app->getContainer()->get(Twig::class);
    $app->add(TwigMiddleware::create($app, $twig));

    // Session middleware — starts/closes PHP session per request (worker-safe)
    $app->add(new SessionMiddleware($twig));

    $app->addErrorMiddleware(
        displayErrorDetails: ($_ENV['APP_ENV'] ?? 'development') !== 'production',
        logErrors: true,
        logErrorDetails: true,
    );
};
