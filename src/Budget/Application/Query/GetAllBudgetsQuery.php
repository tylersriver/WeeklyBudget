<?php

declare(strict_types=1);

namespace App\Budget\Application\Query;

use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\BudgetType;
use App\Transaction\Domain\CategoryRepositoryInterface;

final class GetAllBudgetsQuery
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgets,
        private readonly CategoryRepositoryInterface $categories,
    ) {
    }

    /**
     * @return array{
     *     budgets: array<int, array<string, mixed>>,
     *     budgetTypes: array<BudgetType>,
     *     categories: string[]
     * }
     */
    #[\NoDiscard]
    public function __invoke(int $userId): array
    {
        return [
            'budgets'     => $this->budgets->findAll($userId),
            'budgetTypes' => BudgetType::cases(),
            'categories'  => $this->categories->findAll($userId),
        ];
    }
}
