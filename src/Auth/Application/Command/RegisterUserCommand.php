<?php

declare(strict_types=1);

namespace App\Auth\Application\Command;

readonly class RegisterUserCommand
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}
