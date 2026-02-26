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
    public function __invoke(int $userId): DashboardData
    {
        $activeBudget = $this->budgets->findActive($userId);
        $activeType   = $activeBudget?->getType() ?? BudgetType::Weekly;
        $limit        = $activeBudget?->getAmount() ?? MoneyAmount::zero();

        $spent = $activeType === BudgetType::Weekly
            ? $this->transactions->weeklySpent($userId)
            : $this->transactions->monthlySpent($userId);

        $categoryData = $this->transactions->monthlySpendingByCategory($userId);

        return new DashboardData(
            budget:       new SpendingSummary($activeType, $limit, $spent),
            transactions: $this->transactions->transactionsThisWeek($userId),
            categories:   $this->categories->findAll($userId),
            chartLabels:  array_keys($categoryData),
            chartData:    array_values($categoryData),
        );
    }
}
