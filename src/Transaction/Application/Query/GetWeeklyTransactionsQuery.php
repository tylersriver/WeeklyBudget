<?php

declare(strict_types=1);

namespace App\Transaction\Application\Query;

use App\Transaction\Domain\CategoryRepositoryInterface;
use App\Transaction\Domain\TransactionRepositoryInterface;

final class GetWeeklyTransactionsQuery
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactions,
        private readonly CategoryRepositoryInterface $categories,
    ) {
    }

    /**
     * @return array{transactions: array<int, array<string, mixed>>, categories: string[]}
     */
    #[\NoDiscard]
    public function __invoke(): array
    {
        return [
            'transactions' => $this->transactions->transactionsThisWeek(),
            'categories'   => $this->categories->findAll(),
        ];
    }
}
