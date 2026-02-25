<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure;

use App\Budget\Domain\MoneyAmount;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionRepositoryInterface;
use Cycle\Database\DatabaseManager;

final class CycleTransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private readonly DatabaseManager $dbal,
    ) {}

    public function save(Transaction $transaction): void
    {
        $this->dbal->database()
            ->insert('transactions')
            ->values([
                'type'        => $transaction->getType()->value,
                'description' => $transaction->getDescription()->toString(),
                'amount'      => $transaction->getAmount()->toString(),
                'dateAdded'   => $transaction->getDateAdded()->format('Y-m-d'),
            ])
            ->run();
    }

    #[\NoDiscard]
    public function weeklySpent(): MoneyAmount
    {
        return $this->dbal->database()
            ->query(
                'SELECT COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE WEEKOFYEAR(dateAdded) = WEEKOFYEAR(NOW())
                   AND YEAR(dateAdded) = YEAR(NOW())'
            )
            ->fetch()
            |> (static fn(array $row): MoneyAmount => MoneyAmount::fromFloat((float) $row['total']));
    }

    #[\NoDiscard]
    public function monthlySpent(): MoneyAmount
    {
        return $this->dbal->database()
            ->query(
                'SELECT COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE MONTH(dateAdded) = MONTH(NOW())
                   AND YEAR(dateAdded) = YEAR(NOW())'
            )
            ->fetch()
            |> (static fn(array $row): MoneyAmount => MoneyAmount::fromFloat((float) $row['total']));
    }

    #[\NoDiscard]
    public function transactionsThisWeek(): array
    {
        return $this->dbal->database()
            ->query(
                'SELECT id, DATE_FORMAT(dateAdded, "%m/%d/%Y") AS date,
                        type, description, amount
                 FROM transactions
                 WHERE WEEKOFYEAR(dateAdded) = WEEKOFYEAR(NOW())
                   AND YEAR(dateAdded) = YEAR(NOW())
                 ORDER BY dateAdded DESC'
            )
            ->fetchAll();
    }

    #[\NoDiscard]
    public function transactionsForMonth(int $year, int $month): array
    {
        return $this->dbal->database()
            ->query(
                'SELECT DATE_FORMAT(dateAdded, "%m/%d/%Y") AS date,
                        type, description, amount
                 FROM transactions
                 WHERE MONTH(dateAdded) = ? AND YEAR(dateAdded) = ?
                 ORDER BY dateAdded DESC',
                [$month, $year]
            )
            ->fetchAll();
    }

    #[\NoDiscard]
    public function yearsWithTransactions(): array
    {
        return $this->dbal->database()
            ->query(
                'SELECT YEAR(dateAdded) AS year
                 FROM transactions
                 GROUP BY YEAR(dateAdded)
                 ORDER BY year DESC'
            )
            ->fetchAll()
            |> (static fn(array $rows): array => array_map(
                static fn(array $r): int => (int) $r['year'],
                $rows,
            ));
    }

    #[\NoDiscard]
    public function monthlySpendingByCategory(): array
    {
        return $this->dbal->database()
            ->query(
                'SELECT type, COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE MONTH(dateAdded) = MONTH(NOW())
                   AND YEAR(dateAdded) = YEAR(NOW())
                 GROUP BY type'
            )
            ->fetchAll()
            |> (static fn(array $rows): array => array_column(
                array_map(
                    static fn(array $r): array => ['type' => $r['type'], 'total' => (float) $r['total']],
                    $rows,
                ),
                'total',
                'type',
            ));
    }
}
