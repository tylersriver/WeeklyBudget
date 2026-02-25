<?php

declare(strict_types=1);

namespace App\Shared\Domain;

abstract class AggregateRoot
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function equals(self $other): bool
    {
        return $this->id !== null
            && $other->id !== null
            && static::class === $other::class
            && $this->id === $other->id;
    }
}
