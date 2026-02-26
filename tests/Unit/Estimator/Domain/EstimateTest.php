<?php

declare(strict_types=1);

use App\Budget\Domain\MoneyAmount;
use App\Estimator\Domain\Estimate;
use App\Estimator\Domain\ExpenseCategory;
use App\Estimator\Domain\ExpenseLine;
use App\Estimator\Domain\IncomeLine;

describe('Estimate', function () {
    it('creates an empty estimate with zero totals', function () {
        $estimate = Estimate::empty();

        expect($estimate->totalIncome()->toFloat())->toBe(0.0);
        expect($estimate->totalExpenses()->toFloat())->toBe(0.0);
        expect($estimate->netMonthly())->toBe(0.0);
        expect($estimate->getIncomes())->toBe([]);
        expect($estimate->getExpenses())->toBe([]);
    });

    it('calculates total income from multiple lines', function () {
        $estimate = Estimate::reconstitute(
            [
                new IncomeLine(1, 'Salary', MoneyAmount::fromFloat(5000)),
                new IncomeLine(2, 'Side Gig', MoneyAmount::fromFloat(1200)),
            ],
            [],
        );

        expect($estimate->totalIncome()->toFloat())->toBe(6200.0);
    });

    it('calculates total expenses from multiple lines', function () {
        $estimate = Estimate::reconstitute(
            [],
            [
                new ExpenseLine(1, 'Rent', MoneyAmount::fromFloat(1500), ExpenseCategory::Bills),
                new ExpenseLine(2, 'Groceries', MoneyAmount::fromFloat(400), ExpenseCategory::Spending),
                new ExpenseLine(3, 'Emergency Fund', MoneyAmount::fromFloat(300), ExpenseCategory::Savings),
            ],
        );

        expect($estimate->totalExpenses()->toFloat())->toBe(2200.0);
    });

    it('calculates expenses by category', function () {
        $estimate = Estimate::reconstitute(
            [],
            [
                new ExpenseLine(1, 'Rent', MoneyAmount::fromFloat(1500), ExpenseCategory::Bills),
                new ExpenseLine(2, 'Electric', MoneyAmount::fromFloat(120), ExpenseCategory::Bills),
                new ExpenseLine(3, 'Groceries', MoneyAmount::fromFloat(400), ExpenseCategory::Spending),
                new ExpenseLine(4, 'College Fund', MoneyAmount::fromFloat(500), ExpenseCategory::Savings),
            ],
        );

        expect($estimate->expensesByCategory(ExpenseCategory::Bills)->toFloat())->toBe(1620.0);
        expect($estimate->expensesByCategory(ExpenseCategory::Spending)->toFloat())->toBe(400.0);
        expect($estimate->expensesByCategory(ExpenseCategory::Savings)->toFloat())->toBe(500.0);
    });

    it('calculates positive net monthly when income exceeds expenses', function () {
        $estimate = Estimate::reconstitute(
            [new IncomeLine(1, 'Salary', MoneyAmount::fromFloat(5000))],
            [new ExpenseLine(1, 'Rent', MoneyAmount::fromFloat(1500), ExpenseCategory::Bills)],
        );

        expect($estimate->netMonthly())->toBe(3500.0);
    });

    it('calculates negative net monthly when expenses exceed income', function () {
        $estimate = Estimate::reconstitute(
            [new IncomeLine(1, 'Part-time', MoneyAmount::fromFloat(1000))],
            [new ExpenseLine(1, 'Rent', MoneyAmount::fromFloat(1500), ExpenseCategory::Bills)],
        );

        expect($estimate->netMonthly())->toBe(-500.0);
    });

    it('returns income and expense arrays', function () {
        $incomes = [new IncomeLine(1, 'Salary', MoneyAmount::fromFloat(5000))];
        $expenses = [new ExpenseLine(1, 'Rent', MoneyAmount::fromFloat(1500), ExpenseCategory::Bills)];

        $estimate = Estimate::reconstitute($incomes, $expenses);

        expect($estimate->getIncomes())->toBe($incomes);
        expect($estimate->getExpenses())->toBe($expenses);
    });
});
