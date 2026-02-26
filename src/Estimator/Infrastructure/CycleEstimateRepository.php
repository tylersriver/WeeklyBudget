<?php

declare(strict_types=1);

namespace App\Estimator\Infrastructure;

use App\Budget\Domain\MoneyAmount;
use App\Estimator\Domain\Estimate;
use App\Estimator\Domain\EstimateRepositoryInterface;
use App\Estimator\Domain\ExpenseCategory;
use App\Estimator\Domain\ExpenseLine;
use App\Estimator\Domain\IncomeLine;
use Cycle\Database\DatabaseManager;

final class CycleEstimateRepository implements EstimateRepositoryInterface
{
    public function __construct(
        private readonly DatabaseManager $dbal,
    ) {
    }

    #[\NoDiscard]
    public function find(int $userId): Estimate
    {
        /** @var array<int, array{id: int|string, name: string, amount: int|float|string}> $incomeRows */
        $incomeRows = $this->dbal->database()
            ->query('SELECT id, name, amount FROM estimate_incomes WHERE user_id = ? ORDER BY id', [$userId])
            ->fetchAll();

        /** @var array<int, array{id: int|string, name: string, amount: int|float|string, category: string}> $expenseRows */
        $expenseRows = $this->dbal->database()
            ->query('SELECT id, name, amount, category FROM estimate_expenses WHERE user_id = ? ORDER BY id', [$userId])
            ->fetchAll();

        $incomes = array_map(
            static fn(array $row): IncomeLine => new IncomeLine(
                id:     (int) $row['id'],
                name:   $row['name'],
                amount: MoneyAmount::fromFloat((float) $row['amount']),
            ),
            $incomeRows,
        );

        $expenses = array_map(
            static fn(array $row): ExpenseLine => new ExpenseLine(
                id:       (int) $row['id'],
                name:     $row['name'],
                amount:   MoneyAmount::fromFloat((float) $row['amount']),
                category: ExpenseCategory::from($row['category']),
            ),
            $expenseRows,
        );

        return Estimate::reconstitute($incomes, $expenses);
    }

    public function addIncome(string $name, MoneyAmount $amount, int $userId): void
    {
        $this->dbal->database()
            ->insert('estimate_incomes')
            ->values([
                'name'    => $name,
                'amount'  => $amount->toFloat(),
                'user_id' => $userId,
            ])
            ->run();
    }

    public function removeIncome(int $id): void
    {
        $this->dbal->database()
            ->delete('estimate_incomes', ['id' => $id])
            ->run();
    }

    public function addExpense(string $name, MoneyAmount $amount, ExpenseCategory $category, int $userId): void
    {
        $this->dbal->database()
            ->insert('estimate_expenses')
            ->values([
                'name'     => $name,
                'amount'   => $amount->toFloat(),
                'category' => $category->value,
                'user_id'  => $userId,
            ])
            ->run();
    }

    public function removeExpense(int $id): void
    {
        $this->dbal->database()
            ->delete('estimate_expenses', ['id' => $id])
            ->run();
    }
}
