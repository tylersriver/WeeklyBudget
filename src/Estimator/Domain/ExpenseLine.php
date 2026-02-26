<?php

declare(strict_types=1);

namespace App\Estimator\Domain;

use App\Budget\Domain\MoneyAmount;

readonly class ExpenseLine
{
    public function __construct(
        public ?int $id,
        public string $name,
        public MoneyAmount $amount,
        public ExpenseCategory $category,
    ) {
    }
}
