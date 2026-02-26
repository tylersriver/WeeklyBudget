<?php

declare(strict_types=1);

namespace App\Estimator\Application\Command;

use App\Estimator\Domain\EstimateRepositoryInterface;

final class RemoveExpenseHandler
{
    public function __construct(
        private readonly EstimateRepositoryInterface $estimates,
    ) {
    }

    public function __invoke(RemoveExpenseCommand $command): void
    {
        $this->estimates->removeExpense($command->id);
    }
}
