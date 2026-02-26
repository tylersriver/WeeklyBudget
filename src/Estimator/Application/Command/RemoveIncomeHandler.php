<?php

declare(strict_types=1);

namespace App\Estimator\Application\Command;

use App\Estimator\Domain\EstimateRepositoryInterface;

final class RemoveIncomeHandler
{
    public function __construct(
        private readonly EstimateRepositoryInterface $estimates,
    ) {
    }

    public function __invoke(RemoveIncomeCommand $command): void
    {
        $this->estimates->removeIncome($command->id);
    }
}
