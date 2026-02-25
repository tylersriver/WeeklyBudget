<?php

declare(strict_types=1);

namespace App\Transaction\Application\Command;

use App\Budget\Domain\MoneyAmount;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionDescription;
use App\Transaction\Domain\TransactionRepositoryInterface;
use App\Transaction\Domain\TransactionType;

final class RecordTransactionHandler
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactions,
    ) {}

    public function __invoke(RecordTransactionCommand $command): void
    {
        $type = TransactionType::from($command->type);

        $transaction = Transaction::record(
            type: $type,
            description: TransactionDescription::fromString($command->description),
            amount: MoneyAmount::fromString($command->amount),
            dateAdded: new \DateTimeImmutable($command->date),
        );

        $this->transactions->save($transaction);
    }
}
