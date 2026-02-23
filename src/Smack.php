<?php declare(strict_types=1);

namespace Visifo\SmackClause;

class Smack
{
    public function __construct(
        private readonly mixed $value,
    ) {}

    public static function that(mixed $value): Smack
    {
        if ($value !== null) {
            return new Smack($value);
        }

        throw new SmackException;
    }

    public function isBool(): BoolSmack
    {
        if (is_bool($this->value)) {
            return new BoolSmack($this->value);
        }

        throw new SmackException;
    }

    public function isString(): StringSmack
    {
        if (is_string($this->value)) {
            return new StringSmack($this->value);
        }

        throw new SmackException;
    }

    public function isInt(): IntSmack
    {
        if (is_int($this->value)) {
            return new IntSmack($this->value);
        }

        throw new SmackException;
    }

    public function isFloat(): FloatSmack
    {
        if (is_float($this->value)) {
            return new FloatSmack($this->value);
        }

        throw new SmackException;
    }

    public function isObject(): ObjectSmack
    {
        if (is_object($this->value)) {
            return new ObjectSmack($this->value);
        }

        throw new SmackException;
    }
}
