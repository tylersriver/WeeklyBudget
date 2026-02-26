<?php

declare(strict_types=1);

namespace App\Estimator\Infrastructure\Action;

use App\Estimator\Application\Command\AddExpenseCommand;
use App\Estimator\Application\Command\AddIncomeCommand;
use App\Estimator\Application\Command\RemoveExpenseCommand;
use App\Estimator\Application\Command\RemoveIncomeCommand;
use App\Estimator\Application\Query\GetEstimateQuery;
use App\Shared\Application\CommandBusInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class EstimatorAction
{
    public function __construct(
        private readonly Twig $view,
        private readonly GetEstimateQuery $getEstimate,
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function index(Request $request, Response $response): Response
    {
        /** @var int $userId */
        $userId = $request->getAttribute('userId');
        $data = ($this->getEstimate)($userId);

        return $this->view->render($response, 'estimator.html.twig', $data->toTemplateVars());
    }

    public function addIncome(Request $request, Response $response): Response
    {
        /** @var int $userId */
        $userId = $request->getAttribute('userId');

        /** @var array<string, string> $body */
        $body = (array) $request->getParsedBody();

        $this->commandBus->dispatch(new AddIncomeCommand(
            name:   (string) ($body['name'] ?? ''),
            amount: (string) ($body['amount'] ?? '0'),
            userId: $userId,
        ));

        return $this->redirectToEstimator($request, $response);
    }

    public function removeIncome(Request $request, Response $response): Response
    {
        /** @var array<string, string> $body */
        $body = (array) $request->getParsedBody();

        $this->commandBus->dispatch(new RemoveIncomeCommand(
            id: (int) ($body['id'] ?? 0),
        ));

        return $this->redirectToEstimator($request, $response);
    }

    public function addExpense(Request $request, Response $response): Response
    {
        /** @var int $userId */
        $userId = $request->getAttribute('userId');

        /** @var array<string, string> $body */
        $body = (array) $request->getParsedBody();
        $category = (string) ($body['category'] ?? '');

        $this->commandBus->dispatch(new AddExpenseCommand(
            name:     (string) ($body['name'] ?? ''),
            amount:   (string) ($body['amount'] ?? '0'),
            category: $category,
            userId:   $userId,
        ));

        return $this->redirectToEstimator($request, $response, $category);
    }

    public function removeExpense(Request $request, Response $response): Response
    {
        /** @var array<string, string> $body */
        $body = (array) $request->getParsedBody();
        $category = (string) ($body['category'] ?? '');

        $this->commandBus->dispatch(new RemoveExpenseCommand(
            id: (int) ($body['id'] ?? 0),
        ));

        return $this->redirectToEstimator($request, $response, $category);
    }

    private function redirectToEstimator(
        Request $request,
        Response $response,
        string $tab = '',
    ): Response {
        $url = RouteContext::fromRequest($request)
            ->getRouteParser()
            ->urlFor('estimator');

        if ($tab !== '') {
            $url .= '?' . http_build_query(['tab' => $tab]) . '#expenses';
        }

        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }
}
