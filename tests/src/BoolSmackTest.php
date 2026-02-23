<?php declare(strict_types=1);

use Visifo\SmackClause\BoolSmack;
use Visifo\SmackClause\SmackViolation;

describe('isTrue', function (): void {
    it('throws for false value', function (): void {
        new BoolSmack(false)->isTrue();
    })->throws(SmackViolation::class);

    it('passes for true value', function (): void {
        new BoolSmack(true)->isTrue();
    })->throwsNoExceptions();
});

describe('isFalse', function (): void {
    it('throws for true value', function (): void {
        new BoolSmack(true)->isFalse();
    })->throws(SmackViolation::class);

    it('passes for false value', function (): void {
        new BoolSmack(false)->isFalse();
    })->throwsNoExceptions();
});
