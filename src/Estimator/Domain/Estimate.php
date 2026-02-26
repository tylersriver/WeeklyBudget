<?php

declare(strict_types=1);

namespace App\Estimator\Domain;

use App\Budget\Domain\MoneyAmount;

class Estimate
{
    /**
     * @param IncomeLine[]  $incomes
     * @param ExpenseLine[] $expenses
     */
    private function __construct(
        private array $incomes,
        private array $expenses,
    ) {
    }

    /**
     * Reconstitute from persistence.
     *
     * @param IncomeLine[]  $incomes
     * @param ExpenseLine[] $expenses
     */
    public static function reconstitute(array $incomes, array $expenses): self
    {
        return new self($incomes, $expenses);
    }

    /**
     * Create an empty estimate.
     */
    public static function empty(): self
    {
        return new self([], []);
    }

    /**
     * Sum of all income line amounts.
     */
    #[\NoDiscard]
    public function totalIncome(): MoneyAmount
    {
        $total = 0.0;
        foreach ($this->incomes as $line) {
            $total += $line->amount->toFloat();
        }

        return MoneyAmount::fromFloat($total);
    }

    /**
     * Sum of all expense line amounts.
     */
    #[\NoDiscard]
    public function totalExpenses(): MoneyAmount
    {
        $total = 0.0;
        foreach ($this->expenses as $line) {
            $total += $line->amount->toFloat();
        }

        return MoneyAmount::fromFloat($total);
    }

    /**
     * Sum of expense lines for a given category.
     */
    #[\NoDiscard]
    public function expensesByCategory(ExpenseCategory $category): MoneyAmount
    {
        $total = 0.0;
        foreach ($this->expenses as $line) {
            if ($line->category === $category) {
                $total += $line->amount->toFloat();
            }
        }

        return MoneyAmount::fromFloat($total);
    }

    /**
     * Net monthly gain/loss (income minus expenses). Can be negative.
     */
    #[\NoDiscard]
    public function netMonthly(): float
    {
        return round($this->totalIncome()->toFloat() - $this->totalExpenses()->toFloat(), 2);
    }

    /**
     * @return IncomeLine[]
     */
    public function getIncomes(): array
    {
        return $this->incomes;
    }

    /**
     * @return ExpenseLine[]
     */
    public function getExpenses(): array
    {
        return $this->expenses;
    }
}
