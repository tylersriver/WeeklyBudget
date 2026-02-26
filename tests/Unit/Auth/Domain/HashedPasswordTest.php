<?php

declare(strict_types=1);

use App\Auth\Domain\HashedPassword;

describe('HashedPassword', function () {
    it('hashes and verifies a valid password', function () {
        $password = HashedPassword::fromPlainText('securepass');
        expect($password->verify('securepass'))->toBeTrue();
    });

    it('rejects wrong password on verify', function () {
        $password = HashedPassword::fromPlainText('securepass');
        expect($password->verify('wrongpass'))->toBeFalse();
    });

    it('rejects passwords shorter than 8 characters', function () {
        HashedPassword::fromPlainText('short');
    })->throws(DomainException::class, 'Password must be at least 8 characters.');

    it('reconstitutes from a stored hash', function () {
        $original = HashedPassword::fromPlainText('mypassword');
        $hash = $original->toString();

        $reconstituted = HashedPassword::fromHash($hash);
        expect($reconstituted->verify('mypassword'))->toBeTrue();
    });
});
