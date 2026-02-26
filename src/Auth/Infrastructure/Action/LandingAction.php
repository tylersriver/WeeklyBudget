<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

/**
 * Public marketing / landing page for unauthenticated visitors.
 * Logged-in users are redirected straight to the dashboard.
 */
final class LandingAction
{
    public function __construct(
        private readonly Twig $twig,
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        /** @var int|null $userId */
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId !== null) {
            return $response
                ->withHeader('Location', '/dashboard')
                ->withStatus(302);
        }

        return $this->twig->render($response, 'landing.html.twig');
    }
}
