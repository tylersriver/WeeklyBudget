<?php

declare(strict_types=1);

namespace App\Reporting\Domain;

use App\Budget\Domain\BudgetType;
use App\Budget\Domain\MoneyAmount;

readonly class SpendingSummary
{
    public function __construct(
        public BudgetType $type,
        public MoneyAmount $limit,
        public MoneyAmount $spent,
    ) {
    }

    #[\NoDiscard]
    public function remaining(): MoneyAmount
    {
        return $this->limit->subtract($this->spent);
    }

    #[\NoDiscard]
    public function percentUsed(): float
    {
        $limitFloat = $this->limit->toFloat();
        return $limitFloat > 0
            ? round($this->spent->toFloat() / $limitFloat * 100)
            : 0;
    }
}
