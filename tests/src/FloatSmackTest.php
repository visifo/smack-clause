<?php declare(strict_types=1);

use Visifo\SmackClause\FloatSmack;
use Visifo\SmackClause\SmackViolation;

describe('isPositive', function (): void {
    it('throws for non-positive float', function (float $value): void {
        new FloatSmack($value)->isPositive();
    })->with([0.0, -1.5])->throws(SmackViolation::class);

    it('returns for positive float', function (): void {
        $result = new FloatSmack(1.5)->isPositive();

        expect($result)->toBeInstanceOf(FloatSmack::class);
    });
});

describe('isNegative', function (): void {
    it('throws for non-negative float', function (float $value): void {
        new FloatSmack($value)->isNegative();
    })->with([0.0, 1.5])->throws(SmackViolation::class);

    it('returns for negative float', function (): void {
        $result = new FloatSmack(-1.5)->isNegative();

        expect($result)->toBeInstanceOf(FloatSmack::class);
    });
});
