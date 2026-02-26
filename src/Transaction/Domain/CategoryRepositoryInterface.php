<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

interface CategoryRepositoryInterface
{
    /**
     * @return string[]
     */
    #[\NoDiscard]
    public function findAll(int $userId): array;

    public function exists(string $name, int $userId): bool;

    public function add(string $name, int $userId): void;

    public function delete(string $name, int $userId): void;
}
