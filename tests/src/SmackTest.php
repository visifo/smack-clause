<?php declare(strict_types=1);

use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Smack;
use Visifo\SmackClause\Types\BoolSmack;
use Visifo\SmackClause\Types\FloatSmack;
use Visifo\SmackClause\Types\IntSmack;
use Visifo\SmackClause\Types\ObjectSmack;
use Visifo\SmackClause\Types\StringSmack;

describe('that', function (): void {
    it('throws on null', function (): void {
        Smack::that(null);
    })->throws(SmackException::class, 'Validation failed for `null`: expected non-null value, got `null`.');

    it('includes variable for null input', function (): void {
        $value = null;
        Smack::that($value);
    })->throws(SmackException::class, 'Validation failed for `$value`: expected non-null value, got `null`.');

    it('returns smack instance', function (): void {
        $result = Smack::that('value');

        expect($result)->toBeInstanceOf(Smack::class);
    });
});

describe('isBool', function (): void {
    it('throws for non bool value', function (): void {
        Smack::that('true')->isBool();
    })->throws(SmackException::class, 'Validation failed for `\'true\'`: expected `bool`, got `"true"`.');

    it('throws for non bool variable', function (): void {
        $value = 'true';
        Smack::that($value)->isBool();
    })->throws(SmackException::class, 'Validation failed for `$value`: expected `bool`, got `"true"`.');

    it('throws for non bool expression', function (): void {
        $value = 'true';
        Smack::that($value.'ly')->isBool();
    })->throws(SmackException::class, 'Validation failed for `$value.\'ly\'`: expected `bool`, got `"truely"`.');

    it('returns bool smack for bool value', function (): void {
        $result = Smack::that(true)->isBool();

        expect($result)->toBeInstanceOf(BoolSmack::class);
    });
});

describe('isString', function (): void {
    it('throws for non string value', function (): void {
        Smack::that(123)->isString();
    })->throws(SmackException::class, 'Validation failed for `123`: expected `string`, got `123`.');

    it('throws for non string variable', function (): void {
        $value = 123;
        Smack::that($value)->isString();
    })->throws(SmackException::class, 'Validation failed for `$value`: expected `string`, got `123`.');

    it('throws for non string expression', function (): void {
        $value = 123;
        Smack::that($value + 1)->isString();
    })->throws(SmackException::class, 'Validation failed for `$value + 1`: expected `string`, got `124`.');

    it('returns string smack for string value', function (): void {
        $result = Smack::that('value')->isString();

        expect($result)->toBeInstanceOf(StringSmack::class);
    });
});

describe('isInt', function (): void {
    it('throws for non int value', function (): void {
        Smack::that(1.23)->isInt();
    })->throws(SmackException::class, 'Validation failed for `1.23`: expected `int`, got `1.23`.');

    it('throws for non int variable', function (): void {
        $value = 1.23;
        Smack::that($value)->isInt();
    })->throws(SmackException::class, 'Validation failed for `$value`: expected `int`, got `1.23`.');

    it('throws for non int expression', function (): void {
        $value = 1.23;
        Smack::that($value + 1)->isInt();
    })->throws(SmackException::class, 'Validation failed for `$value + 1`: expected `int`, got `2.23`.');

    it('returns int smack for int value', function (): void {
        $result = Smack::that(123)->isInt();

        expect($result)->toBeInstanceOf(IntSmack::class);
    });
});

describe('isFloat', function (): void {
    it('throws for non float value', function (): void {
        Smack::that(1)->isFloat();
    })->throws(SmackException::class, 'Validation failed for `1`: expected `float`, got `1`.');

    it('throws for non float variable', function (): void {
        $value = 1;
        Smack::that($value)->isFloat();
    })->throws(SmackException::class, 'Validation failed for `$value`: expected `float`, got `1`.');

    it('throws for non float expression', function (): void {
        $value = 1;
        Smack::that($value + 1)->isFloat();
    })->throws(SmackException::class, 'Validation failed for `$value + 1`: expected `float`, got `2`.');

    it('returns float smack for float value', function (): void {
        $result = Smack::that(1.23)->isFloat();

        expect($result)->toBeInstanceOf(FloatSmack::class);
    });
});

describe('isObject', function (): void {
    it('throws for non object value', function (): void {
        Smack::that('value')->isObject();
    })->throws(SmackException::class, 'Validation failed for `\'value\'`: expected `object`, got `"value"`.');

    it('throws for non object variable', function (): void {
        $value = 'value';
        Smack::that($value)->isObject();
    })->throws(SmackException::class, 'Validation failed for `$value`: expected `object`, got `"value"`.');

    it('throws for non object expression', function (): void {
        $value = 'value';
        Smack::that($value.'ly')->isObject();
    })->throws(SmackException::class, 'Validation failed for `$value.\'ly\'`: expected `object`, got `"valuely"`.');

    it('returns object smack for object value', function (): void {
        $result = Smack::that(new stdClass)->isObject();

        expect($result)->toBeInstanceOf(ObjectSmack::class);
    });
});
