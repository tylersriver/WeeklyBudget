<?php

declare(strict_types=1);

namespace App\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

#[Entity(table: 'budgets')]
class Budget
{
    #[Column(type: 'primary')]
    public int $id;

    #[Column(type: 'string')]
    public string $budgetType;

    #[Column(type: 'integer')]
    public int $amount;
}
