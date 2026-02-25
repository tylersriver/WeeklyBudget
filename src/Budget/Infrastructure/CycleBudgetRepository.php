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
        /** @var array{id: int|string, budgetType: string, amount: int|float|string}|false $row */
        $row = $this->dbal->database()
            ->query(
                'SELECT id, budgetType, amount FROM budgets WHERE budgetType = ?',
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
        );
    }

    #[\NoDiscard]
    public function findAll(): array
    {
        /** @var array<int, array<string, mixed>> */
        return $this->dbal->database()
            ->query('SELECT budgetType, amount FROM budgets')
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
}
