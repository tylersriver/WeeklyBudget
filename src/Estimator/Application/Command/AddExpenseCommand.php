<?php

declare(strict_types=1);

namespace App\Estimator\Application\Command;

readonly class AddExpenseCommand
{
    public function __construct(
        public string $name,
        public string $amount,
        public string $category,
    ) {
    }
}
