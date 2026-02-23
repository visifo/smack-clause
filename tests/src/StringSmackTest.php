<?php declare(strict_types=1);

use Visifo\SmackClause\SmackException;
use Visifo\SmackClause\StringSmack;

describe('isNotEmpty', function (): void {
    it('throws for empty string', function (): void {
        new StringSmack('')->isNotEmpty();
    })->throws(SmackException::class);

    it('returns for non-empty string', function (): void {
        $result = new StringSmack('value')->isNotEmpty();

        expect($result)->toBeInstanceOf(StringSmack::class);
    });
});

describe('isNotBlank', function (): void {
    it('throws for blank string', function (): void {
        new StringSmack(' ')->isNotBlank();
    })->throws(SmackException::class);

    it('returns for non-blank string', function (): void {
        $result = new StringSmack('value')->isNotBlank();

        expect($result)->toBeInstanceOf(StringSmack::class);
    });
});
