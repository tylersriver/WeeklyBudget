<?php

declare(strict_types=1);

use App\Budget\Application\Command\UpdateBudgetCommand;
use App\Budget\Application\Command\UpdateBudgetHandler;
use App\Budget\Domain\Budget;
use App\Budget\Domain\BudgetRepositoryInterface;
use App\Budget\Domain\BudgetType;
use App\Budget\Domain\MoneyAmount;

describe('UpdateBudgetHandler', function () {
    it('updates an existing budget and saves it', function () {
        $existing = Budget::reconstitute(1, BudgetType::Weekly, MoneyAmount::fromFloat(200), true);
        $saved = null;

        $repo = Mockery::mock(BudgetRepositoryInterface::class);
        $repo->allows('findByType')->with(BudgetType::Weekly)->andReturn($existing);
        $repo->allows('save')->with(Mockery::on(function (Budget $budget) use (&$saved) {
            $saved = $budget;
            return true;
        }));

        $handler = new UpdateBudgetHandler($repo);
        $result = $handler(new UpdateBudgetCommand(type: 'weekly', amount: 300));

        expect($result)->toBeTrue();
        expect($saved)->not->toBeNull();
        expect($saved->getAmount()->toFloat())->toBe(300.0);
    });

    it('returns false for invalid budget type', function () {
        $repo = Mockery::mock(BudgetRepositoryInterface::class);
        $handler = new UpdateBudgetHandler($repo);

        $result = $handler(new UpdateBudgetCommand(type: 'invalid', amount: 100));

        expect($result)->toBeFalse();
    });

    it('returns false for non-positive amount', function () {
        $repo = Mockery::mock(BudgetRepositoryInterface::class);
        $handler = new UpdateBudgetHandler($repo);

        $result = $handler(new UpdateBudgetCommand(type: 'weekly', amount: 0));

        expect($result)->toBeFalse();
    });

    it('returns false when budget not found', function () {
        $repo = Mockery::mock(BudgetRepositoryInterface::class);
        $repo->allows('findByType')->with(BudgetType::Monthly)->andReturn(null);

        $handler = new UpdateBudgetHandler($repo);
        $result = $handler(new UpdateBudgetCommand(type: 'monthly', amount: 500));

        expect($result)->toBeFalse();
    });
});
