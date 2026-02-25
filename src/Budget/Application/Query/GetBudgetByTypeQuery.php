<?php

declare(strict_types=1);

namespace App\Budget\Application\Query;

use App\Budget\Domain\Budget;
use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\BudgetType;

final class GetBudgetByTypeQuery
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgets,
    ) {}

    #[\NoDiscard]
    public function __invoke(BudgetType $type): ?Budget
    {
        return $this->budgets->findByType($type);
    }
}
