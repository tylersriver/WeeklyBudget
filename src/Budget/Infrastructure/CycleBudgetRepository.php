<?php

declare(strict_types=1);

namespace App\Budget\Infrastructure;

use App\Budget\Domain\Budget;
use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\BudgetType;
use App\Budget\Domain\MoneyAmount;
use Cycle\Database\DatabaseManager;

final class CycleBudgetRepository implements BudgetRepositoryInterface
{
    public function __construct(
        private readonly DatabaseManager $dbal,
    ) {
    }

    #[\NoDiscard]
    public function findByType(BudgetType $type, int $userId): ?Budget
    {
        /** @var array{id: int|string, budgetType: string, amount: int|float|string, active: int|string}|false $row */
        $row = $this->dbal->database()
            ->query(
                'SELECT id, budgetType, amount, active FROM budgets WHERE budgetType = ? AND user_id = ?',
                [$type->value, $userId]
            )
            ->fetch();

        if ($row === false) {
            return null;
        }

        return Budget::reconstitute(
            id:     (int) $row['id'],
            type:   BudgetType::from((string) $row['budgetType']),
            amount: MoneyAmount::fromFloat((float) $row['amount']),
            active: (bool) $row['active'],
        );
    }

    #[\NoDiscard]
    public function findActive(int $userId): ?Budget
    {
        /** @var array{id: int|string, budgetType: string, amount: int|float|string, active: int|string}|false $row */
        $row = $this->dbal->database()
            ->query(
                'SELECT id, budgetType, amount, active FROM budgets WHERE active = 1 AND user_id = ? LIMIT 1',
                [$userId]
            )
            ->fetch();

        if ($row === false) {
            return null;
        }

        return Budget::reconstitute(
            id:     (int) $row['id'],
            type:   BudgetType::from((string) $row['budgetType']),
            amount: MoneyAmount::fromFloat((float) $row['amount']),
            active: true,
        );
    }

    #[\NoDiscard]
    public function findAll(int $userId): array
    {
        /** @var array<int, array<string, mixed>> */
        return $this->dbal->database()
            ->query('SELECT budgetType, amount, active FROM budgets WHERE user_id = ?', [$userId])
            ->fetchAll();
    }

    public function save(Budget $budget, int $userId): void
    {
        $this->dbal->database()
            ->update(
                'budgets',
                ['amount' => (int) $budget->getAmount()->toFloat()],
                ['budgetType' => $budget->getType()->value, 'user_id' => $userId],
            )
            ->run();
    }

    public function activateByType(BudgetType $type, int $userId): void
    {
        $db = $this->dbal->database();

        $db->update('budgets', ['active' => 0], ['user_id' => $userId])->run();
        $db->update('budgets', ['active' => 1], ['budgetType' => $type->value, 'user_id' => $userId])->run();
    }
}
