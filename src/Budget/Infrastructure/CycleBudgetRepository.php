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
    public function findByType(BudgetType $type): ?Budget
    {
        /** @var array{id: int|string, budgetType: string, amount: int|float|string, active: int|string}|false $row */
        $row = $this->dbal->database()
            ->query(
                'SELECT id, budgetType, amount, active FROM budgets WHERE budgetType = ?',
                [$type->value]
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
    public function findActive(): ?Budget
    {
        /** @var array{id: int|string, budgetType: string, amount: int|float|string, active: int|string}|false $row */
        $row = $this->dbal->database()
            ->query('SELECT id, budgetType, amount, active FROM budgets WHERE active = 1 LIMIT 1')
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
    public function findAll(): array
    {
        /** @var array<int, array<string, mixed>> */
        return $this->dbal->database()
            ->query('SELECT budgetType, amount, active FROM budgets')
            ->fetchAll();
    }

    public function save(Budget $budget): void
    {
        $this->dbal->database()
            ->update(
                'budgets',
                ['amount' => (int) $budget->getAmount()->toFloat()],
                ['budgetType' => $budget->getType()->value],
            )
            ->run();
    }

    public function activateByType(BudgetType $type): void
    {
        $db = $this->dbal->database();

        $db->update('budgets', ['active' => 0], [])->run();
        $db->update('budgets', ['active' => 1], ['budgetType' => $type->value])->run();
    }
}
