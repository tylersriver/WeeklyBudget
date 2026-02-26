<?php

declare(strict_types=1);

use App\Auth\Application\Command\RegisterUserCommand;
use App\Auth\Application\Command\RegisterUserHandler;
use App\Auth\Domain\Email;
use App\Auth\Domain\User;
use App\Auth\Domain\UserRepositoryInterface;

describe('RegisterUserHandler', function () {
    it('registers a new user and seeds defaults', function () {
        $saved = null;
        $seeded = false;

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->allows('findByEmail')->with(Mockery::on(
            fn (Email $e) => $e->toString() === 'new@test.com',
        ))->andReturn(null);
        $repo->allows('save')->with(Mockery::on(function (User $u) use (&$saved) {
            $saved = $u;
            return true;
        }))->andReturn(42);
        $repo->allows('seedDefaults')->with(42)->andReturnUsing(function () use (&$seeded): void {
            $seeded = true;
        });

        $handler = new RegisterUserHandler($repo);
        $userId = $handler(new RegisterUserCommand(
            email: 'new@test.com',
            password: 'securepass',
        ));

        expect($userId)->toBe(42);
        expect($saved)->not->toBeNull();
        expect($saved->getEmail()->toString())->toBe('new@test.com');
        expect($seeded)->toBeTrue();
    });

    it('rejects duplicate email', function () {
        $hash = \App\Auth\Domain\HashedPassword::fromHash('h');
        $existing = User::reconstitute(1, Email::fromString('taken@test.com'), $hash);

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->allows('findByEmail')->andReturn($existing);

        $handler = new RegisterUserHandler($repo);
        $handler(new RegisterUserCommand(
            email: 'taken@test.com',
            password: 'securepass',
        ));
    })->throws(DomainException::class, 'An account with this email already exists.');

    it('rejects short password', function () {
        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->allows('findByEmail')->andReturn(null);

        $handler = new RegisterUserHandler($repo);
        $handler(new RegisterUserCommand(
            email: 'new@test.com',
            password: 'short',
        ));
    })->throws(DomainException::class, 'Password must be at least 8 characters.');
});
