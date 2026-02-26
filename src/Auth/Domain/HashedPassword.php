<?php

declare(strict_types=1);

namespace App\Auth\Domain;

readonly class HashedPassword
{
    private function __construct(
        private string $hash,
    ) {
    }

    /**
     * Hash a plain-text password using bcrypt.
     */
    public static function fromPlainText(string $password): self
    {
        if (strlen($password) < 8) {
            throw new \DomainException('Password must be at least 8 characters.');
        }

        return new self(password_hash($password, PASSWORD_BCRYPT));
    }

    /**
     * Reconstitute from a stored hash (no validation).
     */
    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    /**
     * Verify a plain-text password against this hash.
     */
    #[\NoDiscard]
    public function verify(string $plainText): bool
    {
        return password_verify($plainText, $this->hash);
    }

    #[\NoDiscard]
    public function toString(): string
    {
        return $this->hash;
    }
}
