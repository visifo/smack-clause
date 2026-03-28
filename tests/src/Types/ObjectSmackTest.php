<?php declare(strict_types=1);

use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Smack;
use Visifo\SmackClause\Types\ObjectSmack;

describe('that', function (): void {
    describe('isInstanceOf', function (): void {
        it('throws for non-matching class', function (): void {
            Smack::that(new stdClass)->isObject()->isInstanceOf(Exception::class);
        })->throws(
            SmackException::class,
            'Validation failed for `new stdClass`: expected `instance of`, got `object(stdClass)`.',
        );

        it('returns for matching class', function (): void {
            $result = Smack::that(new stdClass)->isObject()->isInstanceOf(stdClass::class);

            expect($result)->toBeInstanceOf(ObjectSmack::class);
        });
    });
});

describe('maybe', function (): void {
    describe('isInstanceOf', function (): void {
        it('skips checks for null value', function (): void {
            $result = Smack::maybe(null)->isObject()->isInstanceOf(stdClass::class);

            expect($result)->toBeInstanceOf(ObjectSmack::class);
        });

        it('returns for matching class', function (): void {
            $result = Smack::maybe(new stdClass)->isObject()->isInstanceOf(stdClass::class);

            expect($result)->toBeInstanceOf(ObjectSmack::class);
        });
    });
});
