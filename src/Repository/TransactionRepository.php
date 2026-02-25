<?php

declare(strict_types=1);

namespace App\Repository;

use Cycle\Database\DatabaseManager;

class TransactionRepository
{
    public function __construct(
        private readonly DatabaseManager $dbal,
    ) {}

    /**
     * Sum of transaction amounts for the current week.
     */
    #[\NoDiscard('Weekly spent total should be used')]
    public function getWeeklySpent(): float
    {
        return $this->dbal->database()
            ->query(
                'SELECT COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE WEEKOFYEAR(dateAdded) = WEEKOFYEAR(NOW())
                   AND YEAR(dateAdded) = YEAR(NOW())'
            )
            ->fetch()
            |> (static fn(array $row): float => (float) $row['total']);
    }

    /**
     * Sum of transaction amounts for the current month.
     */
    #[\NoDiscard('Monthly spent total should be used')]
    public function getMonthlySpent(): float
    {
        return $this->dbal->database()
            ->query(
                'SELECT COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE MONTH(dateAdded) = MONTH(NOW())
                   AND YEAR(dateAdded) = YEAR(NOW())'
            )
            ->fetch()
            |> (static fn(array $row): float => (float) $row['total']);
    }

    /**
     * All transactions for the current week.
     *
     * @return array<int, array<string, mixed>>
     */
    #[\NoDiscard]
    public function getTransactionsThisWeek(): array
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

    /**
     * All transactions for a given month/year.
     *
     * @return array<int, array<string, mixed>>
     */
    #[\NoDiscard]
    public function getTransactionsForMonth(int $year, int $month): array
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

    /**
     * Distinct years that have transactions.
     *
     * @return int[]
     */
    #[\NoDiscard]
    public function getYearsForTransactions(): array
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

    /**
     * Spending totals grouped by transaction type for the current month.
     *
     * @return array<string, float>
     */
    #[\NoDiscard]
    public function getMonthlySpendingByCategory(): array
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

    /**
     * Insert a new transaction.
     */
    public function insert(string $type, string $description, string $amount, string $date): void
    {
        $this->dbal->database()
            ->insert('transactions')
            ->values([
                'type'        => $type,
                'description' => $description,
                'amount'      => $amount,
                'dateAdded'   => $date,
            ])
            ->run();
    }
}
