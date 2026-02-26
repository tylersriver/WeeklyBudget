<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

use App\Budget\Domain\MoneyAmount;

interface TransactionRepositoryInterface
{
    public function save(Transaction $transaction, int $userId): void;

    #[\NoDiscard]
    public function weeklySpent(int $userId): MoneyAmount;

    #[\NoDiscard]
    public function monthlySpent(int $userId): MoneyAmount;

    /**
     * @return array<int, array<string, mixed>>
     */
    #[\NoDiscard]
    public function transactionsThisWeek(int $userId): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    #[\NoDiscard]
    public function transactionsForMonth(int $year, int $month, int $userId): array;

    /**
     * @return int[]
     */
    #[\NoDiscard]
    public function yearsWithTransactions(int $userId): array;

    /**
     * @return array<string, float>
     */
    #[\NoDiscard]
    public function monthlySpendingByCategory(int $userId): array;
}
