<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure\Action;

use App\Transaction\Application\Command\RecordTransactionCommand;
use App\Transaction\Application\Command\RecordTransactionHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

final class TransactionAction
{
    public function __construct(
        private readonly RecordTransactionHandler $recordTransaction,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();

        $type        = $body['type']        ?? '';
        $description = $body['description'] ?? '';
        $amount      = $body['amount']      ?? '0';
        $date        = $body['date']        ?? date('Y-m-d');

        if ($type !== '' && $description !== '') {
            ($this->recordTransaction)(new RecordTransactionCommand(
                type:        $type,
                description: $description,
                amount:      $amount,
                date:        $date,
            ));
        }

        // POST-redirect-GET back to dashboard
        $url = RouteContext::fromRequest($request)->getRouteParser()
            |> (static fn($parser): string => $parser->urlFor('dashboard'));

        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }
}
