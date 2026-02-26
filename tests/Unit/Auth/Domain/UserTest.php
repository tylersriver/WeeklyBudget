<?php

declare(strict_types=1);

use App\Auth\Domain\Email;
use App\Auth\Domain\HashedPassword;
use App\Auth\Domain\User;

describe('User', function () {
    it('registers a new user via factory', function () {
        $user = User::register(
            Email::fromString('test@example.com'),
            HashedPassword::fromPlainText('password123'),
        );

        expect($user->getId())->toBeNull();
        expect($user->getEmail()->toString())->toBe('test@example.com');
        expect($user->getPassword()->verify('password123'))->toBeTrue();
    });

    it('reconstitutes from persistence with id', function () {
        $user = User::reconstitute(
            5,
            Email::fromString('user@test.com'),
            HashedPassword::fromHash('$2y$10$somehash'),
        );

        expect($user->getId())->toBe(5);
        expect($user->getEmail()->toString())->toBe('user@test.com');
    });

    it('checks equality by id', function () {
        $a = User::reconstitute(1, Email::fromString('a@test.com'), HashedPassword::fromHash('h1'));
        $b = User::reconstitute(1, Email::fromString('b@test.com'), HashedPassword::fromHash('h2'));
        $c = User::reconstitute(2, Email::fromString('a@test.com'), HashedPassword::fromHash('h1'));

        expect($a->equals($b))->toBeTrue();
        expect($a->equals($c))->toBeFalse();
    });
});
