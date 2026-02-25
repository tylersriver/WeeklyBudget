<?php

declare(strict_types=1);

namespace App\Reporting\Application\Query;

use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\BudgetType;
use App\Budget\Domain\MoneyAmount;
use App\Reporting\Domain\SpendingSummary;
use App\Transaction\Domain\TransactionRepositoryInterface;
use App\Transaction\Domain\TransactionType;

final class GetDashboardQuery
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgets,
        private readonly TransactionRepositoryInterface $transactions,
    ) {}

    #[\NoDiscard]
    public function __invoke(): DashboardData
    {
        $weeklyBudget  = $this->budgets->findByType(BudgetType::Weekly);
        $monthlyBudget = $this->budgets->findByType(BudgetType::Monthly);

        $weeklyLimit  = $weeklyBudget?->getAmount()  ?? MoneyAmount::zero();
        $monthlyLimit = $monthlyBudget?->getAmount() ?? MoneyAmount::zero();

        $weeklySpent  = $this->transactions->weeklySpent();
        $monthlySpent = $this->transactions->monthlySpent();

        $categoryData = $this->transactions->monthlySpendingByCategory();

        return new DashboardData(
            weekly:           new SpendingSummary(BudgetType::Weekly, $weeklyLimit, $weeklySpent),
            monthly:          new SpendingSummary(BudgetType::Monthly, $monthlyLimit, $monthlySpent),
            transactions:     $this->transactions->transactionsThisWeek(),
            transactionTypes: TransactionType::cases(),
            categories:       array_keys($categoryData),
            categoryTotals:   array_values($categoryData),
        );
    }
}
