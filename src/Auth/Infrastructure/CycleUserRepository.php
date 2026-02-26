<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure;

use App\Auth\Domain\Email;
use App\Auth\Domain\HashedPassword;
use App\Auth\Domain\User;
use App\Auth\Domain\UserRepositoryInterface;
use Cycle\Database\DatabaseManager;

final class CycleUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly DatabaseManager $dbal,
    ) {
    }

    #[\NoDiscard]
    public function findByEmail(Email $email): ?User
    {
        /** @var array{id: int|string, email: string, password_hash: string}|false $row */
        $row = $this->dbal->database()
            ->query('SELECT id, email, password_hash FROM users WHERE email = ?', [$email->toString()])
            ->fetch();

        if ($row === false) {
            return null;
        }

        return User::reconstitute(
            id:       (int) $row['id'],
            email:    Email::fromString($row['email']),
            password: HashedPassword::fromHash($row['password_hash']),
        );
    }

    public function save(User $user): int
    {
        $db = $this->dbal->database();

        $db->insert('users')
            ->values([
                'email'         => $user->getEmail()->toString(),
                'password_hash' => $user->getPassword()->toString(),
                'created_at'    => date('Y-m-d H:i:s'),
            ])
            ->run();

        /** @var array{id: int|string}|false $row */
        $row = $db->query('SELECT last_insert_rowid() AS id')->fetch();

        return (int) ($row !== false ? $row['id'] : 0);
    }

    public function seedDefaults(int $userId): void
    {
        $db = $this->dbal->database();

        // Seed default budgets
        $db->insert('budgets')
            ->values(['budgetType' => 'weekly', 'amount' => 200, 'active' => 1, 'user_id' => $userId])
            ->run();
        $db->insert('budgets')
            ->values(['budgetType' => 'monthly', 'amount' => 800, 'active' => 0, 'user_id' => $userId])
            ->run();

        // Seed default categories
        foreach (['Food', 'Groceries', 'Gas', 'Shopping', 'Other'] as $name) {
            $db->insert('categories')
                ->values(['name' => $name, 'user_id' => $userId])
                ->run();
        }
    }
}
