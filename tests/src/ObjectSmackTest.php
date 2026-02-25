<?php declare(strict_types=1);

use Visifo\SmackClause\ObjectSmack;
use Visifo\SmackClause\Smack;
use Visifo\SmackClause\SmackException;

describe('isInstanceOf', function (): void {
    it('throws for non-matching class', function (): void {
        Smack::that(new stdClass)->isObject()->isInstanceOf(Exception::class);
    })->throws(SmackException::class, 'Validation failed for `new stdClass`: expected `instance of`, got `object(stdClass)`.');

    it('returns for matching class', function (): void {
        $result = Smack::that(new stdClass)->isObject()->isInstanceOf(stdClass::class);

        expect($result)->toBeInstanceOf(ObjectSmack::class);
    });
});
