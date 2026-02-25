<?php

declare(strict_types=1);

namespace App\Action;

use App\Repository\TransactionRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

final class TransactionAction
{
    public function __construct(
        private TransactionRepository $transactions,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $body        = (array) $request->getParsedBody();
        $type        = $body['type']        ?? '';
        $description = $body['description'] ?? '';
        $amount      = $body['amount']      ?? '0';
        $date        = $body['date']        ?? date('Y-m-d');

        if ($type !== '' && $description !== '') {
            $this->transactions->insert($type, $description, $amount, $date);
        }

        // Redirect back to dashboard
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('dashboard');

        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }
}
