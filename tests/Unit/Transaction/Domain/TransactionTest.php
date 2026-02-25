<?php

declare(strict_types=1);

use App\Budget\Domain\MoneyAmount;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionDescription;
use App\Transaction\Domain\TransactionType;

describe('Transaction', function () {
    it('records a new transaction via factory', function () {
        $date = new DateTimeImmutable('2026-02-25');
        $txn = Transaction::record(
            type: TransactionType::Food,
            description: TransactionDescription::fromString('Lunch'),
            amount: MoneyAmount::fromFloat(12.50),
            dateAdded: $date,
        );

        expect($txn->getType())->toBe(TransactionType::Food);
        expect($txn->getDescription()->toString())->toBe('Lunch');
        expect($txn->getAmount()->toFloat())->toBe(12.5);
        expect($txn->getDateAdded())->toBe($date);
        expect($txn->getId())->toBeNull();
    });

    it('rejects zero amount', function () {
        Transaction::record(
            type: TransactionType::Gas,
            description: TransactionDescription::fromString('Fuel'),
            amount: MoneyAmount::zero(),
            dateAdded: new DateTimeImmutable(),
        );
    })->throws(DomainException::class, 'Transaction amount must be positive.');

    it('reconstitutes with an id', function () {
        $txn = Transaction::reconstitute(
            id: 42,
            type: TransactionType::Groceries,
            description: TransactionDescription::fromString('Weekly shop'),
            amount: MoneyAmount::fromFloat(85.00),
            dateAdded: new DateTimeImmutable('2026-01-15'),
        );

        expect($txn->getId())->toBe(42);
        expect($txn->getType())->toBe(TransactionType::Groceries);
    });

    it('checks equality by id', function () {
        $a = Transaction::reconstitute(
            1,
            TransactionType::Food,
            TransactionDescription::fromString('A'),
            MoneyAmount::fromFloat(10),
            new DateTimeImmutable(),
        );
        $b = Transaction::reconstitute(
            1,
            TransactionType::Gas,
            TransactionDescription::fromString('B'),
            MoneyAmount::fromFloat(20),
            new DateTimeImmutable(),
        );
        expect($a->equals($b))->toBeTrue();
    });
});
