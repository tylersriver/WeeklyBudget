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
    public function findAll(): array
    {
        /** @var array<int, array{name: string}> $rows */
        $rows = $this->dbal->database()
            ->query('SELECT name FROM categories ORDER BY name')
            ->fetchAll();

        return array_map(
            static fn(array $row): string => $row['name'],
            $rows,
        );
    }

    public function exists(string $name): bool
    {
        $row = $this->dbal->database()
            ->query('SELECT 1 FROM categories WHERE name = ?', [$name])
            ->fetch();

        return $row !== false;
    }

    public function add(string $name): void
    {
        $this->dbal->database()
            ->insert('categories')
            ->values(['name' => trim($name)])
            ->run();
    }

    public function delete(string $name): void
    {
        $this->dbal->database()
            ->delete('categories', ['name' => $name])
            ->run();
    }
}
