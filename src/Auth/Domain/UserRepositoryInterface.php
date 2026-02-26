<?php

declare(strict_types=1);

namespace App\Auth\Domain;

interface UserRepositoryInterface
{
    #[\NoDiscard]
    public function findByEmail(Email $email): ?User;

    /**
     * Save a new user and return the generated ID.
     */
    public function save(User $user): int;

    /**
     * Seed default budgets and categories for a newly registered user.
     */
    public function seedDefaults(int $userId): void;
}
