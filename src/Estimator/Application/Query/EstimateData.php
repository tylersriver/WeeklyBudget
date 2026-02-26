<?php

declare(strict_types=1);

namespace App\Estimator\Application\Query;

use App\Estimator\Domain\Estimate;
use App\Estimator\Domain\ExpenseCategory;

readonly class EstimateData
{
    /**
     * @param array<ExpenseCategory> $expenseCategories
     */
    public function __construct(
        public Estimate $estimate,
        public array $expenseCategories,
    ) {
    }

    /**
     * Convert to the template variable array expected by estimator.html.twig.
     *
     * @return array<string, mixed>
     */
    #[\NoDiscard]
    public function toTemplateVars(): array
    {
        return [
            'incomes'            => $this->estimate->getIncomes(),
            'expenses'           => $this->estimate->getExpenses(),
            'expenseCategories'  => $this->expenseCategories,
            'totalIncome'        => $this->estimate->totalIncome()->toFloat(),
            'totalBills'         => $this->estimate->expensesByCategory(ExpenseCategory::Bills)->toFloat(),
            'totalSavings'       => $this->estimate->expensesByCategory(ExpenseCategory::Savings)->toFloat(),
            'totalSpending'      => $this->estimate->expensesByCategory(ExpenseCategory::Spending)->toFloat(),
            'totalExpenses'      => $this->estimate->totalExpenses()->toFloat(),
            'netMonthly'         => $this->estimate->netMonthly(),
        ];
    }
}
