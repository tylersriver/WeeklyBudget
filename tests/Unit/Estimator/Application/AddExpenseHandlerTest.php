<?php

declare(strict_types=1);

use App\Budget\Domain\MoneyAmount;
use App\Estimator\Application\Command\AddExpenseCommand;
use App\Estimator\Application\Command\AddExpenseHandler;
use App\Estimator\Domain\EstimateRepositoryInterface;
use App\Estimator\Domain\ExpenseCategory;

describe('AddExpenseHandler', function () {
    it('adds expense via repository', function () {
        $repo = Mockery::mock(EstimateRepositoryInterface::class);
        $repo->allows('addExpense')->with(
            'Rent',
            Mockery::on(fn (MoneyAmount $a) => $a->toFloat() === 1500.0),
            ExpenseCategory::Bills,
            1,
        )->once();

        $handler = new AddExpenseHandler($repo);
        $handler(new AddExpenseCommand(name: 'Rent', amount: '1500', category: 'Bills', userId: 1));
    });

    it('rejects empty name', function () {
        $repo = Mockery::mock(EstimateRepositoryInterface::class);
        $handler = new AddExpenseHandler($repo);

        $handler(new AddExpenseCommand(name: '', amount: '100', category: 'Bills', userId: 1));
    })->throws(DomainException::class, 'Expense name must not be empty.');

    it('rejects invalid category', function () {
        $repo = Mockery::mock(EstimateRepositoryInterface::class);
        $handler = new AddExpenseHandler($repo);

        $handler(new AddExpenseCommand(name: 'Test', amount: '100', category: 'Invalid', userId: 1));
    })->throws(DomainException::class, 'Invalid expense category: Invalid');

    it('rejects non-positive amount', function () {
        $repo = Mockery::mock(EstimateRepositoryInterface::class);
        $handler = new AddExpenseHandler($repo);

        $handler(new AddExpenseCommand(name: 'Rent', amount: '0', category: 'Bills', userId: 1));
    })->throws(DomainException::class, 'Expense amount must be positive.');
});
