<?php

declare(strict_types=1);

namespace App\Estimator\Application\Query;

use App\Estimator\Domain\EstimateRepositoryInterface;
use App\Estimator\Domain\ExpenseCategory;

final class GetEstimateQuery
{
    public function __construct(
        private readonly EstimateRepositoryInterface $estimates,
    ) {
    }

    #[\NoDiscard]
    public function __invoke(int $userId): EstimateData
    {
        return new EstimateData(
            estimate:          $this->estimates->find($userId),
            expenseCategories: ExpenseCategory::cases(),
        );
    }
}
