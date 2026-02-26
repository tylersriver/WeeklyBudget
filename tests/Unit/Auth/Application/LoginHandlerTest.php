<?php

declare(strict_types=1);

use App\Auth\Application\Command\LoginCommand;
use App\Auth\Application\Command\LoginHandler;
use App\Auth\Domain\Email;
use App\Auth\Domain\HashedPassword;
use App\Auth\Domain\User;
use App\Auth\Domain\UserRepositoryInterface;

describe('LoginHandler', function () {
    it('returns user on valid credentials', function () {
        $user = User::reconstitute(
            1,
            Email::fromString('user@test.com'),
            HashedPassword::fromPlainText('password123'),
        );

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->allows('findByEmail')->andReturn($user);

        $handler = new LoginHandler($repo);
        $result = $handler(new LoginCommand(
            email: 'user@test.com',
            password: 'password123',
        ));

        expect($result->getId())->toBe(1);
        expect($result->getEmail()->toString())->toBe('user@test.com');
    });

    it('rejects unknown email', function () {
        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->allows('findByEmail')->andReturn(null);

        $handler = new LoginHandler($repo);
        $handler(new LoginCommand(
            email: 'unknown@test.com',
            password: 'password123',
        ));
    })->throws(DomainException::class, 'Invalid email or password.');

    it('rejects wrong password', function () {
        $user = User::reconstitute(
            1,
            Email::fromString('user@test.com'),
            HashedPassword::fromPlainText('correctpass'),
        );

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->allows('findByEmail')->andReturn($user);

        $handler = new LoginHandler($repo);
        $handler(new LoginCommand(
            email: 'user@test.com',
            password: 'wrongpassword',
        ));
    })->throws(DomainException::class, 'Invalid email or password.');
});
