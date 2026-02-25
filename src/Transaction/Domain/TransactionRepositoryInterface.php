<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

use App\Budget\Domain\MoneyAmount;

interface TransactionRepositoryInterface
{
    public function save(Transaction $transaction): void;

    #[\NoDiscard]
    public function weeklySpent(): MoneyAmount;

    #[\NoDiscard]
    public function monthlySpent(): MoneyAmount;

    /**
     * @return array<int, array<string, mixed>>
     */
    #[\NoDiscard]
    public function transactionsThisWeek(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    #[\NoDiscard]
    public function transactionsForMonth(int $year, int $month): array;

    /**
     * @return int[]
     */
    #[\NoDiscard]
    public function yearsWithTransactions(): array;

    /**
     * @return array<string, float>
     */
    #[\NoDiscard]
    public function monthlySpendingByCategory(): array;
}
