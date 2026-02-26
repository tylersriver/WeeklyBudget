<?php

declare(strict_types=1);

namespace App\Auth\Domain;

use App\Shared\Domain\AggregateRoot;

class User extends AggregateRoot
{
    private function __construct(
        private Email $email,
        private HashedPassword $password,
    ) {
    }

    /**
     * Factory for registering a new user.
     */
    public static function register(Email $email, HashedPassword $password): self
    {
        return new self($email, $password);
    }

    /**
     * Reconstitute from persistence.
     */
    public static function reconstitute(int $id, Email $email, HashedPassword $password): self
    {
        $user = new self($email, $password);
        $user->id = $id;
        return $user;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): HashedPassword
    {
        return $this->password;
    }
}
