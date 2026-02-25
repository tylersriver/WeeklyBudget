<?php

declare(strict_types=1);

namespace App\Action;

use App\Enum\BudgetType;
use App\Enum\TransactionType;
use App\Repository\BudgetRepository;
use App\Repository\TransactionRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class DashboardAction
{
    public function __construct(
        private readonly Twig $view,
        private readonly TransactionRepository $transactions,
        private readonly BudgetRepository $budgets,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $weeklyBudget  = $this->budgets->getBudgetSetting(BudgetType::Weekly);
        $monthlyBudget = $this->budgets->getBudgetSetting(BudgetType::Monthly);
        $weeklySpent   = $this->transactions->getWeeklySpent();
        $monthlySpent  = $this->transactions->getMonthlySpent();
        $categoryData  = $this->transactions->getMonthlySpendingByCategory();

        return $this->view->render($response, 'dashboard.html.twig', [
            'weeklyBudget'     => $weeklyBudget,
            'monthlyBudget'    => $monthlyBudget,
            'weeklySpent'      => $weeklySpent,
            'monthlySpent'     => $monthlySpent,
            'weeklyRemaining'  => $weeklyBudget - $weeklySpent,
            'monthlyRemaining' => $monthlyBudget - $monthlySpent,
            'transactions'     => $this->transactions->getTransactionsThisWeek(),
            'transactionTypes' => TransactionType::cases(),
            'categories'       => array_keys($categoryData),
            'categoryTotals'   => array_values($categoryData),
        ]);
    }
}
