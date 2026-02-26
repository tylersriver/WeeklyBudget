<?php

declare(strict_types=1);

namespace App\Budget\Infrastructure\Action;

use App\Budget\Application\Command\UpdateBudgetCommand;
use App\Budget\Application\Query\GetAllBudgetsQuery;
use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\BudgetType;
use App\Shared\Application\CommandBusInterface;
use App\Transaction\Domain\CategoryRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class BudgetAction
{
    public function __construct(
        private readonly Twig $view,
        private readonly GetAllBudgetsQuery $getAllBudgets,
        private readonly CommandBusInterface $commandBus,
        private readonly BudgetRepositoryInterface $budgets,
        private readonly CategoryRepositoryInterface $categories,
    ) {
    }

    public function index(Request $request, Response $response): Response
    {
        /** @var int $userId */
        $userId = $request->getAttribute('userId');
        $data = ($this->getAllBudgets)($userId);

        return $this->view->render($response, 'budgets.html.twig', $data);
    }

    public function update(Request $request, Response $response): Response
    {
        /** @var int $userId */
        $userId = $request->getAttribute('userId');

        /** @var array<string, string> $body */
        $body   = (array) $request->getParsedBody();
        $type   = (string) ($body['type'] ?? '');
        $amount = (int) ($body['amount'] ?? '0');

        $success = $this->commandBus->dispatch(new UpdateBudgetCommand(
            type:   $type,
            amount: $amount,
            userId: $userId,
        ));

        $data = ($this->getAllBudgets)($userId);

        return $this->view->render($response, 'budgets.html.twig', [
            ...$data,
            'success' => $success,
        ]);
    }

    public function activate(Request $request, Response $response): Response
    {
        /** @var int $userId */
        $userId = $request->getAttribute('userId');

        /** @var array<string, string> $body */
        $body = (array) $request->getParsedBody();
        $type = BudgetType::tryFrom((string) ($body['type'] ?? ''));

        if ($type !== null) {
            $this->budgets->activateByType($type, $userId);
        }

        return $this->redirectToBudgets($request, $response);
    }

    public function addCategory(Request $request, Response $response): Response
    {
        /** @var int $userId */
        $userId = $request->getAttribute('userId');

        /** @var array<string, string> $body */
        $body = (array) $request->getParsedBody();
        $name = trim((string) ($body['name'] ?? ''));

        if ($name !== '' && !$this->categories->exists($name, $userId)) {
            $this->categories->add($name, $userId);
        }

        return $this->redirectToBudgets($request, $response);
    }

    public function deleteCategory(Request $request, Response $response): Response
    {
        /** @var int $userId */
        $userId = $request->getAttribute('userId');

        /** @var array<string, string> $body */
        $body = (array) $request->getParsedBody();
        $name = trim((string) ($body['name'] ?? ''));

        if ($name !== '') {
            $this->categories->delete($name, $userId);
        }

        return $this->redirectToBudgets($request, $response);
    }

    private function redirectToBudgets(Request $request, Response $response): Response
    {
        $url = RouteContext::fromRequest($request)
            ->getRouteParser()
            ->urlFor('budgets');

        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }
}
