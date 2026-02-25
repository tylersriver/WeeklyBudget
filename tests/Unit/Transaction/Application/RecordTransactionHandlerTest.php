<?php

declare(strict_types=1);

use App\Transaction\Application\Command\RecordTransactionCommand;
use App\Transaction\Application\Command\RecordTransactionHandler;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionRepositoryInterface;

describe('RecordTransactionHandler', function () {
    it('records a transaction and saves it', function () {
        $saved = null;

        $repo = Mockery::mock(TransactionRepositoryInterface::class);
        $repo->allows('save')->with(Mockery::on(function (Transaction $txn) use (&$saved) {
            $saved = $txn;
            return true;
        }));

        $handler = new RecordTransactionHandler($repo);
        $handler(new RecordTransactionCommand(
            type: 'Food',
            description: 'Lunch at cafe',
            amount: '15.50',
            date: '2026-02-25',
        ));

        expect($saved)->not->toBeNull();
        expect($saved->getType()->value)->toBe('Food');
        expect($saved->getDescription()->toString())->toBe('Lunch at cafe');
        expect($saved->getAmount()->toFloat())->toBe(15.5);
    });

    it('throws on invalid transaction type', function () {
        $repo = Mockery::mock(TransactionRepositoryInterface::class);
        $handler = new RecordTransactionHandler($repo);

        $handler(new RecordTransactionCommand(
            type: 'InvalidType',
            description: 'Something',
            amount: '10.00',
            date: '2026-02-25',
        ));
    })->throws(ValueError::class);

    it('throws on empty description', function () {
        $repo = Mockery::mock(TransactionRepositoryInterface::class);
        $handler = new RecordTransactionHandler($repo);

        $handler(new RecordTransactionCommand(
            type: 'Food',
            description: '',
            amount: '10.00',
            date: '2026-02-25',
        ));
    })->throws(InvalidArgumentException::class);
});
