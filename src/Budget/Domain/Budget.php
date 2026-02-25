<?php

declare(strict_types=1);

namespace App\Budget\Domain;

use App\Shared\Domain\AggregateRoot;

class Budget extends AggregateRoot
{
    private function __construct(
        private BudgetType $type,
        private MoneyAmount $amount,
    ) {}

    /**
     * Factory for creating a new budget.
     */
    public static function create(BudgetType $type, MoneyAmount $amount): self
    {
        if (!$amount->isPositive()) {
            throw new \DomainException('Budget amount must be positive.');
        }

        return new self($type, $amount);
    }

    /**
     * Reconstitute from persistence — bypasses invariant checks.
     */
    public static function reconstitute(int $id, BudgetType $type, MoneyAmount $amount): self
    {
        $budget = new self($type, $amount);
        $budget->id = $id;
        return $budget;
    }

    /**
     * Update the budget limit. Returns a new instance (immutable pattern).
     */
    public function updateAmount(MoneyAmount $newAmount): self
    {
        if (!$newAmount->isPositive()) {
            throw new \DomainException('Budget amount must be positive.');
        }

        $updated = clone $this;
        $updated->amount = $newAmount;
        return $updated;
    }

    /**
     * Calculate remaining budget given amount already spent.
     */
    #[\NoDiscard]
    public function remaining(MoneyAmount $spent): MoneyAmount
    {
        return $this->amount->subtract($spent);
    }

    /**
     * Percentage of budget used (0–100+).
     */
    #[\NoDiscard]
    public function percentUsed(MoneyAmount $spent): float
    {
        $limit = $this->amount->toFloat();
        return $limit > 0 ? round($spent->toFloat() / $limit * 100) : 0;
    }

    public function getType(): BudgetType
    {
        return $this->type;
    }

    public function getAmount(): MoneyAmount
    {
        return $this->amount;
    }
}
