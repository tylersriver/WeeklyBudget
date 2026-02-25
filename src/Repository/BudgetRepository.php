<?php

declare(strict_types=1);

namespace App\Repository;

use App\Enum\BudgetType;
use Cycle\Database\DatabaseManager;

class BudgetRepository
{
    public function __construct(
        private DatabaseManager $dbal,
    ) {}

    /**
     * Get the budget limit for a given type.
     */
    public function getBudgetSetting(BudgetType $type): int
    {
        $row = $this->dbal->database()
            ->query(
                'SELECT amount FROM budgets WHERE budgetType = ?',
                [$type->value]
            )
            ->fetch();

        return (int) ($row['amount'] ?? 0);
    }

    /**
     * Get all current budgets.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        return $this->dbal->database()
            ->query('SELECT budgetType, amount FROM budgets')
            ->fetchAll();
    }

    /**
     * Update a budget amount by type.
     */
    public function update(BudgetType $type, int $amount): void
    {
        $this->dbal->database()
            ->update('budgets', ['amount' => $amount], ['budgetType' => $type->value])
            ->run();
    }
}
