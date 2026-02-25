<?php

declare(strict_types=1);

use App\Budget\Domain\Budget;
use App\Budget\Domain\BudgetType;
use App\Budget\Domain\MoneyAmount;

describe('Budget', function () {
    it('creates via factory with valid data', function () {
        $budget = Budget::create(BudgetType::Weekly, MoneyAmount::fromFloat(200));
        expect($budget->getType())->toBe(BudgetType::Weekly);
        expect($budget->getAmount()->toFloat())->toBe(200.0);
        expect($budget->getId())->toBeNull();
    });

    it('rejects zero amount on create', function () {
        Budget::create(BudgetType::Monthly, MoneyAmount::zero());
    })->throws(DomainException::class, 'Budget amount must be positive.');

    it('reconstitutes from persistence with id', function () {
        $budget = Budget::reconstitute(5, BudgetType::Monthly, MoneyAmount::fromFloat(800));
        expect($budget->getId())->toBe(5);
        expect($budget->getType())->toBe(BudgetType::Monthly);
        expect($budget->getAmount()->toFloat())->toBe(800.0);
    });

    it('returns a new instance on updateAmount', function () {
        $original = Budget::create(BudgetType::Weekly, MoneyAmount::fromFloat(200));
        $updated = $original->updateAmount(MoneyAmount::fromFloat(300));

        expect($updated)->not->toBe($original);
        expect($updated->getAmount()->toFloat())->toBe(300.0);
        expect($original->getAmount()->toFloat())->toBe(200.0);
    });

    it('rejects zero amount on update', function () {
        $budget = Budget::create(BudgetType::Weekly, MoneyAmount::fromFloat(200));
        $budget->updateAmount(MoneyAmount::zero());
    })->throws(DomainException::class, 'Budget amount must be positive.');

    it('calculates remaining budget', function () {
        $budget = Budget::create(BudgetType::Weekly, MoneyAmount::fromFloat(200));
        $remaining = $budget->remaining(MoneyAmount::fromFloat(75));
        expect($remaining->toFloat())->toBe(125.0);
    });

    it('calculates percent used', function () {
        $budget = Budget::create(BudgetType::Weekly, MoneyAmount::fromFloat(200));
        expect($budget->percentUsed(MoneyAmount::fromFloat(100)))->toBe(50.0);
        expect($budget->percentUsed(MoneyAmount::fromFloat(200)))->toBe(100.0);
        expect($budget->percentUsed(MoneyAmount::zero()))->toBe(0.0);
    });

    it('calculates percent used over 100', function () {
        $budget = Budget::create(BudgetType::Weekly, MoneyAmount::fromFloat(100));
        expect($budget->percentUsed(MoneyAmount::fromFloat(150)))->toBe(150.0);
    });

    it('checks equality by id', function () {
        $a = Budget::reconstitute(1, BudgetType::Weekly, MoneyAmount::fromFloat(200));
        $b = Budget::reconstitute(1, BudgetType::Weekly, MoneyAmount::fromFloat(300));
        $c = Budget::reconstitute(2, BudgetType::Monthly, MoneyAmount::fromFloat(200));

        expect($a->equals($b))->toBeTrue();
        expect($a->equals($c))->toBeFalse();
    });

    it('treats unsaved entities as not equal', function () {
        $a = Budget::create(BudgetType::Weekly, MoneyAmount::fromFloat(200));
        $b = Budget::create(BudgetType::Weekly, MoneyAmount::fromFloat(200));
        expect($a->equals($b))->toBeFalse();
    });
});
