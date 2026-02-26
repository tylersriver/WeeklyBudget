<?php

declare(strict_types=1);

namespace App\Estimator\Application\Command;

use App\Budget\Domain\MoneyAmount;
use App\Estimator\Domain\EstimateRepositoryInterface;

final class AddIncomeHandler
{
    public function __construct(
        private readonly EstimateRepositoryInterface $estimates,
    ) {
    }

    public function __invoke(AddIncomeCommand $command): void
    {
        $name = trim($command->name);
        if ($name === '') {
            throw new \DomainException('Income name must not be empty.');
        }

        $amount = MoneyAmount::fromString($command->amount);
        if (!$amount->isPositive()) {
            throw new \DomainException('Income amount must be positive.');
        }

        $this->estimates->addIncome($name, $amount);
    }
}
