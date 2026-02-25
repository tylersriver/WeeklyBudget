<?php

declare(strict_types=1);

use App\Budget\Domain\MoneyAmount;

describe('MoneyAmount', function () {
    it('creates from a valid string', function () {
        $amount = MoneyAmount::fromString('42.50');
        expect($amount->toString())->toBe('42.50');
        expect($amount->toFloat())->toBe(42.5);
    });

    it('creates from a float', function () {
        $amount = MoneyAmount::fromFloat(99.99);
        expect($amount->toString())->toBe('99.99');
    });

    it('normalises to two decimal places', function () {
        $amount = MoneyAmount::fromString('10');
        expect($amount->toString())->toBe('10.00');
    });

    it('creates a zero instance', function () {
        $zero = MoneyAmount::zero();
        expect($zero->toString())->toBe('0.00');
        expect($zero->toFloat())->toBe(0.0);
        expect($zero->isPositive())->toBeFalse();
    });

    it('rejects negative amounts', function () {
        MoneyAmount::fromString('-5');
    })->throws(InvalidArgumentException::class);

    it('rejects non-numeric strings', function () {
        MoneyAmount::fromString('abc');
    })->throws(InvalidArgumentException::class);

    it('subtracts correctly', function () {
        $a = MoneyAmount::fromString('100.00');
        $b = MoneyAmount::fromString('30.50');
        $result = $a->subtract($b);
        expect($result->toString())->toBe('69.50');
    });

    it('compares greater than', function () {
        $a = MoneyAmount::fromString('100.00');
        $b = MoneyAmount::fromString('50.00');
        expect($a->isGreaterThan($b))->toBeTrue();
        expect($b->isGreaterThan($a))->toBeFalse();
    });

    it('detects positive amounts', function () {
        expect(MoneyAmount::fromString('0.01')->isPositive())->toBeTrue();
        expect(MoneyAmount::fromString('0.00')->isPositive())->toBeFalse();
    });

    it('compares equality by value', function () {
        $a = MoneyAmount::fromString('25.00');
        $b = MoneyAmount::fromFloat(25.0);
        expect($a->equals($b))->toBeTrue();
    });

    it('detects inequality', function () {
        $a = MoneyAmount::fromString('25.00');
        $b = MoneyAmount::fromString('25.01');
        expect($a->equals($b))->toBeFalse();
    });
});
