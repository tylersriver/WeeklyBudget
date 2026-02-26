<?php

declare(strict_types=1);

namespace App\Estimator\Application\Command;

use App\Budget\Domain\MoneyAmount;
use App\Estimator\Domain\EstimateRepositoryInterface;
use App\Estimator\Domain\ExpenseCategory;

final class AddExpenseHandler
{
    public function __construct(
        private readonly EstimateRepositoryInterface $estimates,
    ) {
    }

    public function __invoke(AddExpenseCommand $command): void
    {
        $name = trim($command->name);
        if ($name === '') {
            throw new \DomainException('Expense name must not be empty.');
        }

        $category = ExpenseCategory::tryFrom($command->category);
        if ($category === null) {
            throw new \DomainException("Invalid expense category: {$command->category}");
        }

        $amount = MoneyAmount::fromString($command->amount);
        if (!$amount->isPositive()) {
            throw new \DomainException('Expense amount must be positive.');
        }

        $this->estimates->addExpense($name, $amount, $category, $command->userId);
    }
}
