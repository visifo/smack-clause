<?php declare(strict_types=1);

use Visifo\SmackClause\IntSmack;
use Visifo\SmackClause\SmackException;

describe('isPositive', function (): void {
    it('throws for non-positive int', function (int $value): void {
        new IntSmack($value)->isPositive();
    })->with([0, -1])->throws(SmackException::class);

    it('returns for positive int', function (): void {
        $result = new IntSmack(1)->isPositive();

        expect($result)->toBeInstanceOf(IntSmack::class);
    });
});

describe('isNegative', function (): void {
    it('throws for non-negative int', function (int $value): void {
        new IntSmack($value)->isNegative();
    })->with([0, 1])->throws(SmackException::class);

    it('returns for negative int', function (): void {
        $result = new IntSmack(-1)->isNegative();

        expect($result)->toBeInstanceOf(IntSmack::class);
    });
});
