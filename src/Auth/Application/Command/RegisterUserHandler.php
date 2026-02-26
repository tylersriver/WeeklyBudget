<?php

declare(strict_types=1);

namespace App\Auth\Application\Command;

use App\Auth\Domain\Email;
use App\Auth\Domain\HashedPassword;
use App\Auth\Domain\User;
use App\Auth\Domain\UserRepositoryInterface;

final class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    /**
     * Register a new user and seed default data.
     *
     * @return int The new user's ID.
     */
    public function __invoke(RegisterUserCommand $command): int
    {
        $email = Email::fromString($command->email);

        if ($this->users->findByEmail($email) !== null) {
            throw new \DomainException('An account with this email already exists.');
        }

        $password = HashedPassword::fromPlainText($command->password);
        $user = User::register($email, $password);

        $userId = $this->users->save($user);
        $this->users->seedDefaults($userId);

        return $userId;
    }
}
