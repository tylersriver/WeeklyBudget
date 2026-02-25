<?php

declare(strict_types=1);

namespace App\Budget\Infrastructure\Action;

use App\Budget\Application\Command\UpdateBudgetCommand;
use App\Budget\Application\Command\UpdateBudgetHandler;
use App\Budget\Application\Query\GetAllBudgetsQuery;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class BudgetAction
{
    public function __construct(
        private readonly Twig $view,
        private readonly GetAllBudgetsQuery $getAllBudgets,
        private readonly UpdateBudgetHandler $updateBudget,
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $data = ($this->getAllBudgets)();

        return $this->view->render($response, 'budgets.html.twig', $data);
    }

    public function update(Request $request, Response $response): Response
    {
        $body   = (array) $request->getParsedBody();
        $type   = $body['type']   ?? '';
        $amount = (int) ($body['amount'] ?? 0);

        $success = ($this->updateBudget)(new UpdateBudgetCommand(
            type:   $type,
            amount: $amount,
        ));

        $data = ($this->getAllBudgets)();

        return $this->view->render($response, 'budgets.html.twig', [
            ...$data,
            'success' => $success,
        ]);
    }
}
