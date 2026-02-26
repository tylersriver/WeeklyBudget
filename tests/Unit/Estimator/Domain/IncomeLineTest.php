<?php

declare(strict_types=1);

use App\Budget\Domain\MoneyAmount;
use App\Estimator\Domain\IncomeLine;

describe('IncomeLine', function () {
    it('creates with valid name and amount', function () {
        $line = new IncomeLine(1, 'Salary', MoneyAmount::fromFloat(5000));

        expect($line->id)->toBe(1);
        expect($line->name)->toBe('Salary');
        expect($line->amount->toFloat())->toBe(5000.0);
    });

    it('allows null id for unsaved lines', function () {
        $line = new IncomeLine(null, 'Bonus', MoneyAmount::fromFloat(200));

        expect($line->id)->toBeNull();
        expect($line->name)->toBe('Bonus');
    });
});
