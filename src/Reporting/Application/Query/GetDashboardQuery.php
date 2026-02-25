<?php

declare(strict_types=1);

namespace App\Reporting\Application\Query;

use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\BudgetType;
use App\Budget\Domain\MoneyAmount;
use App\Reporting\Domain\SpendingSummary;
use App\Transaction\Domain\CategoryRepositoryInterface;
use App\Transaction\Domain\TransactionRepositoryInterface;

final class GetDashboardQuery
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgets,
        private readonly TransactionRepositoryInterface $transactions,
        private readonly CategoryRepositoryInterface $categories,
    ) {
    }

    #[\NoDiscard]
    public function __invoke(): DashboardData
    {
        $activeBudget = $this->budgets->findActive();
        $activeType   = $activeBudget?->getType() ?? BudgetType::Weekly;
        $limit        = $activeBudget?->getAmount() ?? MoneyAmount::zero();

        $spent = $activeType === BudgetType::Weekly
            ? $this->transactions->weeklySpent()
            : $this->transactions->monthlySpent();

        $categoryData = $this->transactions->monthlySpendingByCategory();

        return new DashboardData(
            budget:       new SpendingSummary($activeType, $limit, $spent),
            transactions: $this->transactions->transactionsThisWeek(),
            categories:   $this->categories->findAll(),
            chartLabels:  array_keys($categoryData),
            chartData:    array_values($categoryData),
        );
    }
}
