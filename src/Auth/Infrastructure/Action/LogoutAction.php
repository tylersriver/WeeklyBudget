<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class LogoutAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        $_SESSION = [];
        session_destroy();

        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}
