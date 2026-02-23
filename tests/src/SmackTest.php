<?php declare(strict_types=1);

use Visifo\SmackClause\BoolSmack;
use Visifo\SmackClause\FloatSmack;
use Visifo\SmackClause\IntSmack;
use Visifo\SmackClause\ObjectSmack;
use Visifo\SmackClause\Smack;
use Visifo\SmackClause\SmackException;
use Visifo\SmackClause\StringSmack;

describe('that', function (): void {
    it('throws on null', function (): void {
        Smack::that(null);
    })->throws(SmackException::class);

    it('returns smack instance', function (): void {
        $result = Smack::that('value');

        expect($result)->toBeInstanceOf(Smack::class);
    });
});

describe('isBool', function (): void {
    it('throws for non bool values', function (): void {
        Smack::that('true')->isBool();
    })->throws(SmackException::class);

    it('returns bool smack for bool values', function (): void {
        $result = Smack::that(true)->isBool();

        expect($result)->toBeInstanceOf(BoolSmack::class);
    });
});

describe('isString', function (): void {
    it('throws for non string values', function (): void {
        Smack::that(123)->isString();
    })->throws(SmackException::class);

    it('returns string smack for string values', function (): void {
        $result = Smack::that('value')->isString();

        expect($result)->toBeInstanceOf(StringSmack::class);
    });
});

describe('isInt', function (): void {
    it('throws for non int values', function (): void {
        Smack::that(1.23)->isInt();
    })->throws(SmackException::class);

    it('returns int smack for int values', function (): void {
        $result = Smack::that(123)->isInt();

        expect($result)->toBeInstanceOf(IntSmack::class);
    });
});

describe('isFloat', function (): void {
    it('throws for non float values', function (): void {
        Smack::that(1)->isFloat();
    })->throws(SmackException::class);

    it('returns float smack for float values', function (): void {
        $result = Smack::that(1.23)->isFloat();

        expect($result)->toBeInstanceOf(FloatSmack::class);
    });
});

describe('isObject', function (): void {
    it('throws for non object values', function (): void {
        Smack::that('value')->isObject();
    })->throws(SmackException::class);

    it('returns object smack for object values', function (): void {
        $result = Smack::that(new stdClass)->isObject();

        expect($result)->toBeInstanceOf(ObjectSmack::class);
    });
});
