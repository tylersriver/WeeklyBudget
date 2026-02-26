<?php

declare(strict_types=1);

namespace App\Auth\Application\Command;

use App\Auth\Domain\Email;
use App\Auth\Domain\User;
use App\Auth\Domain\UserRepositoryInterface;

final class LoginHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    /**
     * Validate credentials and return the authenticated user.
     */
    #[\NoDiscard]
    public function __invoke(LoginCommand $command): User
    {
        $email = Email::fromString($command->email);
        $user = $this->users->findByEmail($email);

        if ($user === null || !$user->getPassword()->verify($command->password)) {
            throw new \DomainException('Invalid email or password.');
        }

        return $user;
    }
}
