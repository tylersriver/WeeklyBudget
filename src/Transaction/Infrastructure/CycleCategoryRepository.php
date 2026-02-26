<?php

declare(strict_types=1);

namespace App\Transaction\Infrastructure;

use App\Transaction\Domain\CategoryRepositoryInterface;
use Cycle\Database\DatabaseManager;

final class CycleCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private readonly DatabaseManager $dbal,
    ) {
    }

    #[\NoDiscard]
    public function findAll(int $userId): array
    {
        /** @var array<int, array{name: string}> $rows */
        $rows = $this->dbal->database()
            ->query('SELECT name FROM categories WHERE user_id = ? ORDER BY name', [$userId])
            ->fetchAll();

        return array_map(
            static fn(array $row): string => $row['name'],
            $rows,
        );
    }

    public function exists(string $name, int $userId): bool
    {
        $row = $this->dbal->database()
            ->query('SELECT 1 FROM categories WHERE name = ? AND user_id = ?', [$name, $userId])
            ->fetch();

        return $row !== false;
    }

    public function add(string $name, int $userId): void
    {
        $this->dbal->database()
            ->insert('categories')
            ->values(['name' => trim($name), 'user_id' => $userId])
            ->run();
    }

    public function delete(string $name, int $userId): void
    {
        $this->dbal->database()
            ->delete('categories', ['name' => $name, 'user_id' => $userId])
            ->run();
    }
}
