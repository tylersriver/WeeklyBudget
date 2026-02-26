<?php

declare(strict_types=1);

use App\Budget\Domain\MoneyAmount;
use App\Estimator\Application\Command\AddIncomeCommand;
use App\Estimator\Application\Command\AddIncomeHandler;
use App\Estimator\Domain\EstimateRepositoryInterface;

describe('AddIncomeHandler', function () {
    it('adds income via repository', function () {
        $repo = Mockery::mock(EstimateRepositoryInterface::class);
        $repo->allows('addIncome')->with(
            'Salary',
            Mockery::on(fn (MoneyAmount $a) => $a->toFloat() === 3000.0),
        )->once();

        $handler = new AddIncomeHandler($repo);
        $handler(new AddIncomeCommand(name: 'Salary', amount: '3000'));
    });

    it('rejects empty name', function () {
        $repo = Mockery::mock(EstimateRepositoryInterface::class);
        $handler = new AddIncomeHandler($repo);

        $handler(new AddIncomeCommand(name: '  ', amount: '100'));
    })->throws(DomainException::class, 'Income name must not be empty.');

    it('rejects non-positive amount', function () {
        $repo = Mockery::mock(EstimateRepositoryInterface::class);
        $handler = new AddIncomeHandler($repo);

        $handler(new AddIncomeCommand(name: 'Salary', amount: '0'));
    })->throws(DomainException::class, 'Income amount must be positive.');
});
