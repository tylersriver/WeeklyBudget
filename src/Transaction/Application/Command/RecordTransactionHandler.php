<?php

declare(strict_types=1);

namespace App\Transaction\Application\Command;

use App\Budget\Domain\MoneyAmount;
use App\Transaction\Domain\CategoryRepositoryInterface;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionDescription;
use App\Transaction\Domain\TransactionRepositoryInterface;

final class RecordTransactionHandler
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactions,
        private readonly CategoryRepositoryInterface $categories,
    ) {
    }

    public function __invoke(RecordTransactionCommand $command): void
    {
        if (!$this->categories->exists($command->type)) {
            throw new \DomainException("Invalid category: {$command->type}");
        }

        $transaction = Transaction::record(
            type: $command->type,
            description: TransactionDescription::fromString($command->description),
            amount: MoneyAmount::fromString($command->amount),
            dateAdded: new \DateTimeImmutable($command->date),
        );

        $this->transactions->save($transaction);
    }
}
