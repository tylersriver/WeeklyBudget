<?php

declare(strict_types=1);

use App\Transaction\Domain\TransactionDescription;

describe('TransactionDescription', function () {
    it('creates from a valid string', function () {
        $desc = TransactionDescription::fromString('Lunch at cafe');
        expect($desc->toString())->toBe('Lunch at cafe');
    });

    it('trims whitespace', function () {
        $desc = TransactionDescription::fromString('  Coffee  ');
        expect($desc->toString())->toBe('Coffee');
    });

    it('rejects empty strings', function () {
        TransactionDescription::fromString('');
    })->throws(InvalidArgumentException::class, 'Transaction description cannot be empty.');

    it('rejects whitespace-only strings', function () {
        TransactionDescription::fromString('   ');
    })->throws(InvalidArgumentException::class);

    it('compares equality by value', function () {
        $a = TransactionDescription::fromString('Groceries');
        $b = TransactionDescription::fromString('Groceries');
        $c = TransactionDescription::fromString('Gas');
        expect($a->equals($b))->toBeTrue();
        expect($a->equals($c))->toBeFalse();
    });
});
