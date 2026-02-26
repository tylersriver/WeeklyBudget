<?php

declare(strict_types=1);

namespace App\Budget\Application\Command;

use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\BudgetType;
use App\Budget\Domain\MoneyAmount;

final class UpdateBudgetHandler
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgets,
    ) {
    }

    /**
     * @return bool Whether the update succeeded.
     */
    public function __invoke(UpdateBudgetCommand $command): bool
    {
        $type = BudgetType::tryFrom($command->type);

        if ($type === null || $command->amount <= 0) {
            return false;
        }

        $budget = $this->budgets->findByType($type, $command->userId);

        if ($budget === null) {
            return false;
        }

        $updated = $budget->updateAmount(MoneyAmount::fromFloat((float) $command->amount));
        $this->budgets->save($updated, $command->userId);

        return true;
    }
}
