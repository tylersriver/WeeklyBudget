<?php

declare(strict_types=1);

namespace App\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

#[Entity(table: 'transactions')]
class Transaction
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'date', name: 'dateAdded')]
    public \DateTimeImmutable $dateAdded;

    #[Column(type: 'string')]
    public string $type;

    #[Column(type: 'string')]
    public string $description;

    #[Column(type: 'decimal(10,2)')]
    public string $amount;
}
