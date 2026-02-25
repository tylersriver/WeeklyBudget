<?php

declare(strict_types=1);

namespace App\Budget\Domain;

readonly class MoneyAmount
{
    private function __construct(
        private string $amount,
    ) {
    }

    public static function fromString(string $amount): self
    {
        if (!is_numeric($amount) || (float) $amount < 0) {
            throw new \InvalidArgumentException("Invalid money amount: {$amount}");
        }

        return new self(number_format((float) $amount, 2, '.', ''));
    }

    public static function fromFloat(float $amount): self
    {
        return self::fromString((string) $amount);
    }

    public static function zero(): self
    {
        return new self('0.00');
    }

    public function toFloat(): float
    {
        return (float) $this->amount;
    }

    public function toString(): string
    {
        return $this->amount;
    }

    public function subtract(self $other): self
    {
        $result = (float) $this->amount - (float) $other->amount;
        return new self(number_format($result, 2, '.', ''));
    }

    public function isGreaterThan(self $other): bool
    {
        return (float) $this->amount > (float) $other->amount;
    }

    public function isPositive(): bool
    {
        return (float) $this->amount > 0;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount;
    }
}
