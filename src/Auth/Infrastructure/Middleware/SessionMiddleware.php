<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Views\Twig;

/**
 * Starts a PHP session at the beginning of each request and writes it
 * at the end. Also injects session user data into Twig globals so
 * the layout template can render auth-aware UI.
 *
 * Safe for FrankenPHP worker mode — sessions are scoped per-request
 * via start/write_close lifecycle.
 */
final class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Twig $twig,
    ) {
    }

    public function process(Request $request, Handler $handler): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Inject session user info into Twig globals for layout rendering
        $this->twig->getEnvironment()->addGlobal('authUser', [
            'id'    => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
        ]);

        $response = $handler->handle($request);

        session_write_close();

        return $response;
    }
}
