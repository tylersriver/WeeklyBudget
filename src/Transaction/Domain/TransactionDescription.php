<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

readonly class TransactionDescription
{
    private function __construct(
        private string $value,
    ) {}

    public static function fromString(string $value): self
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new \InvalidArgumentException('Transaction description cannot be empty.');
        }

        return new self($trimmed);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
