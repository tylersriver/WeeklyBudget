<?php

declare(strict_types=1);

namespace App\Transaction\Application\Query;

use App\Transaction\Domain\TransactionRepositoryInterface;
use App\Transaction\Domain\TransactionType;

final class GetWeeklyTransactionsQuery
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactions,
    ) {
    }

    /**
     * @return array{transactions: array<int, array<string, mixed>>, transactionTypes: array<TransactionType>}
     */
    #[\NoDiscard]
    public function __invoke(): array
    {
        return [
            'transactions'     => $this->transactions->transactionsThisWeek(),
            'transactionTypes' => TransactionType::cases(),
        ];
    }
}
