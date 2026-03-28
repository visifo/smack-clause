<?php declare(strict_types=1);

use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Smack;
use Visifo\SmackClause\Types\IntSmack;

describe('that', function (): void {
    describe('isPositive', function (): void {
        it('throws for zero int', function (): void {
            Smack::that(0)->isInt()->isPositive();
        })->throws(SmackException::class, 'Validation failed for `0`: expected `positive int`, got `0`.');

        it('throws for negative int', function (): void {
            Smack::that(-1)->isInt()->isPositive();
        })->throws(SmackException::class, 'Validation failed for `-1`: expected `positive int`, got `-1`.');

        it('returns for positive int', function (): void {
            $result = Smack::that(1)->isInt()->isPositive();

            expect($result)->toBeInstanceOf(IntSmack::class);
        });
    });

    describe('isNegative', function (): void {
        it('throws for zero int', function (): void {
            Smack::that(0)->isInt()->isNegative();
        })->throws(SmackException::class, 'Validation failed for `0`: expected `negative int`, got `0`.');

        it('throws for positive int', function (): void {
            Smack::that(1)->isInt()->isNegative();
        })->throws(SmackException::class, 'Validation failed for `1`: expected `negative int`, got `1`.');

        it('returns for negative int', function (): void {
            $result = Smack::that(-1)->isInt()->isNegative();

            expect($result)->toBeInstanceOf(IntSmack::class);
        });
    });
});

describe('maybe', function (): void {
    describe('isPositive', function (): void {
        it('skips checks for null value', function (): void {
            $result = Smack::maybe(null)->isInt()->isPositive();

            expect($result)->toBeInstanceOf(IntSmack::class);
        });

        it('returns for positive int', function (): void {
            $result = Smack::maybe(1)->isInt()->isPositive();

            expect($result)->toBeInstanceOf(IntSmack::class);
        });
    });

    describe('isNegative', function (): void {
        it('skips checks for null value', function (): void {
            $result = Smack::maybe(null)->isInt()->isNegative();

            expect($result)->toBeInstanceOf(IntSmack::class);
        });

        it('returns for negative int', function (): void {
            $result = Smack::maybe(-1)->isInt()->isNegative();

            expect($result)->toBeInstanceOf(IntSmack::class);
        });
    });
});
