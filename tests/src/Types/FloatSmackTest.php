<?php declare(strict_types=1);

use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Smack;
use Visifo\SmackClause\Types\FloatSmack;

describe('that', function (): void {
    describe('isPositive', function (): void {
        it('throws for zero float', function (): void {
            Smack::that(0.0)->isFloat()->isPositive();
        })->throws(SmackException::class, 'Validation failed for `0.0`: expected `positive float`, got `0`.');

        it('throws for negative float', function (): void {
            Smack::that(-1.5)->isFloat()->isPositive();
        })->throws(SmackException::class, 'Validation failed for `-1.5`: expected `positive float`, got `-1.5`.');

        it('returns for positive float', function (): void {
            $result = Smack::that(1.5)->isFloat()->isPositive();

            expect($result)->toBeInstanceOf(FloatSmack::class);
        });
    });

    describe('isNegative', function (): void {
        it('throws for zero float', function (): void {
            Smack::that(0.0)->isFloat()->isNegative();
        })->throws(SmackException::class, 'Validation failed for `0.0`: expected `negative float`, got `0`.');

        it('throws for positive float', function (): void {
            Smack::that(1.5)->isFloat()->isNegative();
        })->throws(SmackException::class, 'Validation failed for `1.5`: expected `negative float`, got `1.5`.');

        it('returns for negative float', function (): void {
            $result = Smack::that(-1.5)->isFloat()->isNegative();

            expect($result)->toBeInstanceOf(FloatSmack::class);
        });
    });
});

describe('maybe', function (): void {
    describe('isPositive', function (): void {
        it('skips checks for null value', function (): void {
            $result = Smack::maybe(null)->isFloat()->isPositive();

            expect($result)->toBeInstanceOf(FloatSmack::class);
        });

        it('returns for positive float', function (): void {
            $result = Smack::maybe(1.5)->isFloat()->isPositive();

            expect($result)->toBeInstanceOf(FloatSmack::class);
        });
    });

    describe('isNegative', function (): void {
        it('skips checks for null value', function (): void {
            $result = Smack::maybe(null)->isFloat()->isNegative();

            expect($result)->toBeInstanceOf(FloatSmack::class);
        });

        it('returns for negative float', function (): void {
            $result = Smack::maybe(-1.5)->isFloat()->isNegative();

            expect($result)->toBeInstanceOf(FloatSmack::class);
        });
    });
});
