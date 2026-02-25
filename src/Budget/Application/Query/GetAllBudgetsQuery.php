<?php

declare(strict_types=1);

namespace App\Budget\Application\Query;

use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\BudgetType;

final class GetAllBudgetsQuery
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgets,
    ) {}

    /**
     * @return array{budgets: array<int, array<string, mixed>>, budgetTypes: array<BudgetType>}
     */
    #[\NoDiscard]
    public function __invoke(): array
    {
        return [
            'budgets'     => $this->budgets->findAll(),
            'budgetTypes' => BudgetType::cases(),
        ];
    }
}
