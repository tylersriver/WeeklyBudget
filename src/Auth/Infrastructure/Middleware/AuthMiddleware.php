<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

/**
 * Protects routes by requiring an authenticated session.
 * Sets 'userId' and 'userEmail' request attributes for downstream handlers.
 */
final class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        /** @var int|null $userId */
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId === null) {
            return (new SlimResponse())
                ->withHeader('Location', '/')
                ->withStatus(302);
        }

        /** @var string $email */
        $email = $_SESSION['user_email'] ?? '';

        $request = $request
            ->withAttribute('userId', (int) $userId)
            ->withAttribute('userEmail', (string) $email);

        return $handler->handle($request);
    }
}
