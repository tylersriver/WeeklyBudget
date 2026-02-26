<?php

declare(strict_types=1);

namespace App\Estimator\Domain;

use App\Budget\Domain\MoneyAmount;

interface EstimateRepositoryInterface
{
    /**
     * Load the full estimate (all income + expense lines).
     */
    #[\NoDiscard]
    public function find(): Estimate;

    public function addIncome(string $name, MoneyAmount $amount): void;

    public function removeIncome(int $id): void;

    public function addExpense(string $name, MoneyAmount $amount, ExpenseCategory $category): void;

    public function removeExpense(int $id): void;
}
