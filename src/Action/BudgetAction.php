<?php

declare(strict_types=1);

namespace App\Action;

use App\Enum\BudgetType;
use App\Repository\BudgetRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class BudgetAction
{
    public function __construct(
        private readonly Twig $view,
        private readonly BudgetRepository $budgets,
    ) {}

    public function index(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'budgets.html.twig', [
            'budgets'     => $this->budgets->getAll(),
            'budgetTypes' => BudgetType::cases(),
        ]);
    }

    public function update(Request $request, Response $response): Response
    {
        $body   = (array) $request->getParsedBody();
        $type   = BudgetType::tryFrom($body['type'] ?? '');
        $amount = (int) ($body['amount'] ?? 0);

        if ($type !== null && $amount > 0) {
            $this->budgets->update($type, $amount);
        }

        return $this->view->render($response, 'budgets.html.twig', [
            'budgets'     => $this->budgets->getAll(),
            'budgetTypes' => BudgetType::cases(),
            'success'     => $type !== null,
        ]);
    }
}
