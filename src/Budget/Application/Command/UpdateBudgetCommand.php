<?php

declare(strict_types=1);

namespace App\Budget\Application\Command;

readonly class UpdateBudgetCommand
{
    public function __construct(
        public string $type,
        public int $amount,
    ) {}
}
