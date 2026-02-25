<?php

declare(strict_types=1);

use App\Budget\Domain\BudgetType;
use App\Budget\Domain\MoneyAmount;
use App\Reporting\Domain\SpendingSummary;

describe('SpendingSummary', function () {
    it('calculates remaining amount', function () {
        $summary = new SpendingSummary(
            type: BudgetType::Weekly,
            limit: MoneyAmount::fromFloat(200),
            spent: MoneyAmount::fromFloat(75),
        );

        expect($summary->remaining()->toFloat())->toBe(125.0);
    });

    it('calculates percent used', function () {
        $summary = new SpendingSummary(
            type: BudgetType::Monthly,
            limit: MoneyAmount::fromFloat(800),
            spent: MoneyAmount::fromFloat(400),
        );

        expect($summary->percentUsed())->toBe(50.0);
    });

    it('returns zero percent when limit is zero', function () {
        $summary = new SpendingSummary(
            type: BudgetType::Weekly,
            limit: MoneyAmount::zero(),
            spent: MoneyAmount::fromFloat(50),
        );

        expect($summary->percentUsed())->toBe(0.0);
    });

    it('allows overspending beyond 100 percent', function () {
        $summary = new SpendingSummary(
            type: BudgetType::Weekly,
            limit: MoneyAmount::fromFloat(100),
            spent: MoneyAmount::fromFloat(250),
        );

        expect($summary->percentUsed())->toBe(250.0);
    });
});
