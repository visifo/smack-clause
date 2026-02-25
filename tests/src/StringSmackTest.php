<?php declare(strict_types=1);

use Visifo\SmackClause\Smack;
use Visifo\SmackClause\SmackException;
use Visifo\SmackClause\StringSmack;

describe('isNotEmpty', function (): void {
    it('throws for empty string', function (): void {
        Smack::that('')->isString()->isNotEmpty();
    })->throws(SmackException::class, 'Validation failed for `\'\'`: expected `not empty`, got `""`.');

    it('returns for non-empty string', function (): void {
        $result = Smack::that('value')->isString()->isNotEmpty();

        expect($result)->toBeInstanceOf(StringSmack::class);
    });
});

describe('isNotBlank', function (): void {
    it('throws for blank string', function (): void {
        Smack::that(' ')->isString()->isNotBlank();
    })->throws(SmackException::class, 'Validation failed for `\' \'`: expected `not blank`, got `" "`.');

    it('returns for non-blank string', function (): void {
        $result = Smack::that('value')->isString()->isNotBlank();

        expect($result)->toBeInstanceOf(StringSmack::class);
    });
});
