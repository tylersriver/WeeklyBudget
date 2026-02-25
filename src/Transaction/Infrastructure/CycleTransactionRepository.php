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
                "SELECT COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE strftime('%W', dateAdded) = strftime('%W', 'now')
                   AND strftime('%Y', dateAdded) = strftime('%Y', 'now')"
            )
            ->fetch()
            |> (static fn(array $row): MoneyAmount => MoneyAmount::fromFloat((float) $row['total']));
    }

    #[\NoDiscard]
    public function monthlySpent(): MoneyAmount
    {
        return $this->dbal->database()
            ->query(
                "SELECT COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE strftime('%m', dateAdded) = strftime('%m', 'now')
                   AND strftime('%Y', dateAdded) = strftime('%Y', 'now')"
            )
            ->fetch()
            |> (static fn(array $row): MoneyAmount => MoneyAmount::fromFloat((float) $row['total']));
    }

    #[\NoDiscard]
    public function transactionsThisWeek(): array
    {
        return $this->dbal->database()
            ->query(
                "SELECT id,
                        strftime('%m/%d/%Y', dateAdded) AS date,
                        type, description, amount
                 FROM transactions
                 WHERE strftime('%W', dateAdded) = strftime('%W', 'now')
                   AND strftime('%Y', dateAdded) = strftime('%Y', 'now')
                 ORDER BY dateAdded DESC"
            )
            ->fetchAll();
    }

    #[\NoDiscard]
    public function transactionsForMonth(int $year, int $month): array
    {
        $m = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        $y = (string) $year;

        return $this->dbal->database()
            ->query(
                "SELECT strftime('%m/%d/%Y', dateAdded) AS date,
                        type, description, amount
                 FROM transactions
                 WHERE strftime('%m', dateAdded) = ?
                   AND strftime('%Y', dateAdded) = ?
                 ORDER BY dateAdded DESC",
                [$m, $y]
            )
            ->fetchAll();
    }

    #[\NoDiscard]
    public function yearsWithTransactions(): array
    {
        return $this->dbal->database()
            ->query(
                "SELECT CAST(strftime('%Y', dateAdded) AS INTEGER) AS year
                 FROM transactions
                 GROUP BY strftime('%Y', dateAdded)
                 ORDER BY year DESC"
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
                "SELECT type, COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE strftime('%m', dateAdded) = strftime('%m', 'now')
                   AND strftime('%Y', dateAdded) = strftime('%Y', 'now')
                 GROUP BY type"
            )
            ->fetchAll()
            |> (static fn(array $rows): array => array_column(
                array_map(
                    static fn(array $r): array => [
                        'type' => $r['type'],
                        'total' => (float) $r['total'],
                    ],
                    $rows,
                ),
                'total',
                'type',
            ));
    }
}
