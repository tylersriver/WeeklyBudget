<?php

declare(strict_types=1);

namespace App\Estimator\Application\Command;

readonly class RemoveIncomeCommand
{
    public function __construct(
        public int $id,
    ) {
    }
}
