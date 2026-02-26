<?php

declare(strict_types=1);

use App\Transaction\Application\Command\RecordTransactionCommand;
use App\Transaction\Application\Command\RecordTransactionHandler;
use App\Transaction\Domain\CategoryRepositoryInterface;
use App\Transaction\Domain\Transaction;
use App\Transaction\Domain\TransactionRepositoryInterface;

describe('RecordTransactionHandler', function () {
    it('records a transaction and saves it', function () {
        $saved = null;

        $repo = Mockery::mock(TransactionRepositoryInterface::class);
        $repo->allows('save')->with(Mockery::on(function (Transaction $txn) use (&$saved) {
            $saved = $txn;
            return true;
        }), 1);

        $categories = Mockery::mock(CategoryRepositoryInterface::class);
        $categories->allows('exists')->with('Food', 1)->andReturn(true);

        $handler = new RecordTransactionHandler($repo, $categories);
        $handler(new RecordTransactionCommand(
            type: 'Food',
            description: 'Lunch at cafe',
            amount: '15.50',
            date: '2026-02-25',
            userId: 1,
        ));

        expect($saved)->not->toBeNull();
        expect($saved->getType())->toBe('Food');
        expect($saved->getDescription()->toString())->toBe('Lunch at cafe');
        expect($saved->getAmount()->toFloat())->toBe(15.5);
    });

    it('throws on invalid category', function () {
        $repo = Mockery::mock(TransactionRepositoryInterface::class);

        $categories = Mockery::mock(CategoryRepositoryInterface::class);
        $categories->allows('exists')->with('InvalidType', 1)->andReturn(false);

        $handler = new RecordTransactionHandler($repo, $categories);

        $handler(new RecordTransactionCommand(
            type: 'InvalidType',
            description: 'Something',
            amount: '10.00',
            date: '2026-02-25',
            userId: 1,
        ));
    })->throws(DomainException::class, 'Invalid category');

    it('throws on empty description', function () {
        $repo = Mockery::mock(TransactionRepositoryInterface::class);

        $categories = Mockery::mock(CategoryRepositoryInterface::class);
        $categories->allows('exists')->with('Food', 1)->andReturn(true);

        $handler = new RecordTransactionHandler($repo, $categories);

        $handler(new RecordTransactionCommand(
            type: 'Food',
            description: '',
            amount: '10.00',
            date: '2026-02-25',
            userId: 1,
        ));
    })->throws(InvalidArgumentException::class);
});
