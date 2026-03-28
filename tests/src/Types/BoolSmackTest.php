<?php declare(strict_types=1);

use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Smack;

describe('that', function (): void {
    describe('isTrue', function (): void {
        it('throws for false value', function (): void {
            Smack::that(false)->isBool()->isTrue();
        })->throws(SmackException::class, 'Validation failed for `false`: expected `true`, got `false`.');

        it('passes for true value', function (): void {
            Smack::that(true)->isBool()->isTrue();
        })->throwsNoExceptions();
    });

    describe('isFalse', function (): void {
        it('throws for true value', function (): void {
            Smack::that(true)->isBool()->isFalse();
        })->throws(SmackException::class, 'Validation failed for `true`: expected `false`, got `true`.');

        it('passes for false value', function (): void {
            Smack::that(false)->isBool()->isFalse();
        })->throwsNoExceptions();
    });
});

describe('maybe', function (): void {
    describe('isTrue', function (): void {
        it('skips checks for null value', function (): void {
            Smack::maybe(null)->isBool()->isTrue();
        })->throwsNoExceptions();

        it('throws for false value', function (): void {
            Smack::maybe(false)->isBool()->isTrue();
        })->throws(SmackException::class, 'Validation failed for `false`: expected `true`, got `false`.');
    });

    describe('isFalse', function (): void {
        it('skips checks for null value', function (): void {
            Smack::maybe(null)->isBool()->isFalse();
        })->throwsNoExceptions();

        it('throws for true value', function (): void {
            Smack::maybe(true)->isBool()->isFalse();
        })->throws(SmackException::class, 'Validation failed for `true`: expected `false`, got `true`.');
    });
});
