<?php declare(strict_types=1);

use Visifo\SmackClause\ObjectSmack;
use Visifo\SmackClause\SmackViolation;

describe('isInstanceOf', function (): void {
    it('throws for non-matching class', function (): void {
        new ObjectSmack(new stdClass)->isInstanceOf(Exception::class);
    })->throws(SmackViolation::class);

    it('returns for matching class', function (): void {
        $result = new ObjectSmack(new stdClass)->isInstanceOf(stdClass::class);

        expect($result)->toBeInstanceOf(ObjectSmack::class);
    });
});
