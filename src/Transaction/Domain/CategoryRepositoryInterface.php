<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

interface CategoryRepositoryInterface
{
    /**
     * @return string[]
     */
    #[\NoDiscard]
    public function findAll(): array;

    public function exists(string $name): bool;

    public function add(string $name): void;

    public function delete(string $name): void;
}
