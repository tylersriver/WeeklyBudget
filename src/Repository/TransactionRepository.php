<?php

declare(strict_types=1);

namespace App\Repository;

use Cycle\Database\DatabaseManager;

class TransactionRepository
{
    public function __construct(
        private DatabaseManager $dbal,
    ) {}

    /**
     * Sum of transaction amounts for the current week.
     */
    public function getWeeklySpent(): float
    {
        $row = $this->dbal->database()
            ->query(
                'SELECT COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE WEEKOFYEAR(dateAdded) = WEEKOFYEAR(NOW())
                   AND YEAR(dateAdded) = YEAR(NOW())'
            )
            ->fetch();

        return (float) $row['total'];
    }

    /**
     * Sum of transaction amounts for the current month.
     */
    public function getMonthlySpent(): float
    {
        $row = $this->dbal->database()
            ->query(
                'SELECT COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE MONTH(dateAdded) = MONTH(NOW())
                   AND YEAR(dateAdded) = YEAR(NOW())'
            )
            ->fetch();

        return (float) $row['total'];
    }

    /**
     * All transactions for the current week.
     *
     * @return array<int, array<string, mixed>>
     */
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
    public function getYearsForTransactions(): array
    {
        $rows = $this->dbal->database()
            ->query(
                'SELECT YEAR(dateAdded) AS year
                 FROM transactions
                 GROUP BY YEAR(dateAdded)
                 ORDER BY year DESC'
            )
            ->fetchAll();

        return array_map(fn(array $r): int => (int) $r['year'], $rows);
    }

    /**
     * Spending totals grouped by transaction type for the current month.
     *
     * @return array<string, float>
     */
    public function getMonthlySpendingByCategory(): array
    {
        $rows = $this->dbal->database()
            ->query(
                'SELECT type, COALESCE(SUM(amount), 0) AS total
                 FROM transactions
                 WHERE MONTH(dateAdded) = MONTH(NOW())
                   AND YEAR(dateAdded) = YEAR(NOW())
                 GROUP BY type'
            )
            ->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $result[$row['type']] = (float) $row['total'];
        }
        return $result;
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
