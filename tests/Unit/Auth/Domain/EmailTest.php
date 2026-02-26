<?php

declare(strict_types=1);

use App\Auth\Domain\Email;

describe('Email', function () {
    it('creates from a valid email string', function () {
        $email = Email::fromString('user@example.com');
        expect($email->toString())->toBe('user@example.com');
    });

    it('normalises to lowercase and trims whitespace', function () {
        $email = Email::fromString('  User@Example.COM  ');
        expect($email->toString())->toBe('user@example.com');
    });

    it('rejects invalid email format', function () {
        Email::fromString('not-an-email');
    })->throws(DomainException::class, 'Invalid email address.');

    it('rejects empty string', function () {
        Email::fromString('');
    })->throws(DomainException::class, 'Invalid email address.');

    it('compares equality by value', function () {
        $a = Email::fromString('user@example.com');
        $b = Email::fromString('USER@example.com');
        $c = Email::fromString('other@example.com');

        expect($a->equals($b))->toBeTrue();
        expect($a->equals($c))->toBeFalse();
    });
});
