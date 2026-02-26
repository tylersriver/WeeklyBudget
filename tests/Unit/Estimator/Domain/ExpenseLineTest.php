<?php

declare(strict_types=1);

use App\Budget\Domain\MoneyAmount;
use App\Estimator\Domain\ExpenseCategory;
use App\Estimator\Domain\ExpenseLine;

describe('ExpenseLine', function () {
    it('creates with valid name, amount, and category', function () {
        $line = new ExpenseLine(1, 'Rent', MoneyAmount::fromFloat(1500), ExpenseCategory::Bills);

        expect($line->id)->toBe(1);
        expect($line->name)->toBe('Rent');
        expect($line->amount->toFloat())->toBe(1500.0);
        expect($line->category)->toBe(ExpenseCategory::Bills);
    });

    it('allows null id for unsaved lines', function () {
        $line = new ExpenseLine(null, 'Groceries', MoneyAmount::fromFloat(400), ExpenseCategory::Spending);

        expect($line->id)->toBeNull();
    });

    it('supports all expense categories', function () {
        $bills = new ExpenseLine(1, 'Rent', MoneyAmount::fromFloat(100), ExpenseCategory::Bills);
        $savings = new ExpenseLine(2, 'Fund', MoneyAmount::fromFloat(100), ExpenseCategory::Savings);
        $spending = new ExpenseLine(3, 'Food', MoneyAmount::fromFloat(100), ExpenseCategory::Spending);

        expect($bills->category)->toBe(ExpenseCategory::Bills);
        expect($savings->category)->toBe(ExpenseCategory::Savings);
        expect($spending->category)->toBe(ExpenseCategory::Spending);
    });
});
