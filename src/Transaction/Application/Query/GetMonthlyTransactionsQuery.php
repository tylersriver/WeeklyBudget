<?php

declare(strict_types=1);

namespace App\Transaction\Application\Query;

use App\Transaction\Domain\TransactionRepositoryInterface;

final class GetMonthlyTransactionsQuery
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactions,
    ) {
    }

    /**
     * @return array{
     *     transactions: array<int, array<string, mixed>>,
     *     years: int[],
     *     selectedMonth: int,
     *     selectedYear: int,
     * }
     */
    #[\NoDiscard]
    public function __invoke(int $month, int $year): array
    {
        return [
            'transactions'  => $this->transactions->transactionsForMonth($year, $month),
            'years'         => $this->transactions->yearsWithTransactions(),
            'selectedMonth' => $month,
            'selectedYear'  => $year,
        ];
    }
}
