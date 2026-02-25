<?php

declare(strict_types=1);

namespace App\Budget\Domain;

interface BudgetRepositoryInterface
{
    #[\NoDiscard]
    public function findByType(BudgetType $type): ?Budget;

    #[\NoDiscard]
    public function findActive(): ?Budget;

    /**
     * @return array<int, array<string, mixed>>
     */
    #[\NoDiscard]
    public function findAll(): array;

    public function save(Budget $budget): void;

    /**
     * Deactivate all budgets, then activate the given type.
     */
    public function activateByType(BudgetType $type): void;
}
