<?php

declare(strict_types=1);

namespace App\Reporting\Application\Query;

use App\Reporting\Domain\SpendingSummary;
use App\Transaction\Domain\TransactionType;

readonly class DashboardData
{
    /**
     * @param array<int, array<string, mixed>> $transactions
     * @param array<TransactionType>            $transactionTypes
     * @param string[]                          $categories
     * @param float[]                           $categoryTotals
     */
    public function __construct(
        public SpendingSummary $weekly,
        public SpendingSummary $monthly,
        public array $transactions,
        public array $transactionTypes,
        public array $categories,
        public array $categoryTotals,
    ) {
    }

    /**
     * Convert to the template variable array expected by dashboard.html.twig.
     *
     * @return array<string, mixed>
     */
    #[\NoDiscard]
    public function toTemplateVars(): array
    {
        return [
            'weeklyBudget'     => $this->weekly->limit->toFloat(),
            'monthlyBudget'    => $this->monthly->limit->toFloat(),
            'weeklySpent'      => $this->weekly->spent->toFloat(),
            'monthlySpent'     => $this->monthly->spent->toFloat(),
            'weeklyRemaining'  => $this->weekly->remaining()->toFloat(),
            'monthlyRemaining' => $this->monthly->remaining()->toFloat(),
            'transactions'     => $this->transactions,
            'transactionTypes' => $this->transactionTypes,
            'categories'       => $this->categories,
            'categoryTotals'   => $this->categoryTotals,
        ];
    }
}
