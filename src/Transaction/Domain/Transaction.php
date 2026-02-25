<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

use App\Budget\Domain\MoneyAmount;
use App\Shared\Domain\AggregateRoot;

class Transaction extends AggregateRoot
{
    private function __construct(
        private string $type,
        private TransactionDescription $description,
        private MoneyAmount $amount,
        private \DateTimeImmutable $dateAdded,
    ) {
    }

    /**
     * Factory for recording a new transaction. Enforces all invariants.
     */
    public static function record(
        string $type,
        TransactionDescription $description,
        MoneyAmount $amount,
        \DateTimeImmutable $dateAdded,
    ): self {
        if (trim($type) === '') {
            throw new \DomainException('Transaction type must not be empty.');
        }

        if (!$amount->isPositive()) {
            throw new \DomainException('Transaction amount must be positive.');
        }

        return new self($type, $description, $amount, $dateAdded);
    }

    /**
     * Reconstitute from persistence — bypasses invariant checks.
     */
    public static function reconstitute(
        int $id,
        string $type,
        TransactionDescription $description,
        MoneyAmount $amount,
        \DateTimeImmutable $dateAdded,
    ): self {
        $txn = new self($type, $description, $amount, $dateAdded);
        $txn->id = $id;
        return $txn;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): TransactionDescription
    {
        return $this->description;
    }

    public function getAmount(): MoneyAmount
    {
        return $this->amount;
    }

    public function getDateAdded(): \DateTimeImmutable
    {
        return $this->dateAdded;
    }
}
