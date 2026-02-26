<?php

declare(strict_types=1);

namespace App\Budget\Domain;

interface BudgetRepositoryInterface
{
    #[\NoDiscard]
    public function findByType(BudgetType $type, int $userId): ?Budget;

    #[\NoDiscard]
    public function findActive(int $userId): ?Budget;

    /**
     * @return array<int, array<string, mixed>>
     */
    #[\NoDiscard]
    public function findAll(int $userId): array;

    public function save(Budget $budget, int $userId): void;

    /**
     * Deactivate all budgets, then activate the given type.
     */
    public function activateByType(BudgetType $type, int $userId): void;
}
