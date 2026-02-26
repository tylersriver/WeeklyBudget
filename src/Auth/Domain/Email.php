<?php

declare(strict_types=1);

namespace App\Auth\Domain;

readonly class Email
{
    private function __construct(
        private string $value,
    ) {
    }

    /**
     * Create from a raw string, validating format.
     */
    public static function fromString(string $email): self
    {
        $email = trim(strtolower($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException('Invalid email address.');
        }

        return new self($email);
    }

    #[\NoDiscard]
    public function toString(): string
    {
        return $this->value;
    }

    #[\NoDiscard]
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
