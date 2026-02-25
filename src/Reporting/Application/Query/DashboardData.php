<?php

declare(strict_types=1);

namespace App\Reporting\Application\Query;

use App\Reporting\Domain\SpendingSummary;

readonly class DashboardData
{
    /**
     * @param array<int, array<string, mixed>> $transactions
     * @param string[]                          $categories
     * @param string[]                          $chartLabels
     * @param float[]                           $chartData
     */
    public function __construct(
        public SpendingSummary $budget,
        public array $transactions,
        public array $categories,
        public array $chartLabels,
        public array $chartData,
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
            'budgetType'      => $this->budget->type->value,
            'budgetLabel'     => ucfirst($this->budget->type->value),
            'budgetLimit'     => $this->budget->limit->toFloat(),
            'budgetSpent'     => $this->budget->spent->toFloat(),
            'budgetRemaining' => $this->budget->remaining()->toFloat(),
            'transactions'    => $this->transactions,
            'categories'      => $this->categories,
            'chartLabels'     => $this->chartLabels,
            'chartData'       => $this->chartData,
        ];
    }
}
