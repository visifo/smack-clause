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

        throw new SmackViolation;
    }

    public function isBool(): BoolSmack
    {
        if (is_bool($this->value)) {
            return new BoolSmack((bool) $this->value);
        }

        throw new SmackViolation;
    }

    public function isString(): StringSmack
    {
        if (is_string($this->value)) {
            return new StringSmack((string) $this->value);
        }

        throw new SmackViolation;
    }

    public function isInt(): IntSmack
    {
        if (is_int($this->value)) {
            return new IntSmack((int) $this->value);
        }

        throw new SmackViolation;
    }

    public function isFloat(): FloatSmack
    {
        if (is_float($this->value)) {
            return new FloatSmack((float) $this->value);
        }

        throw new SmackViolation;
    }

    public function isObject(): ObjectSmack
    {
        if (is_object($this->value)) {
            return new ObjectSmack((object) $this->value);
        }

        throw new SmackViolation;
    }
}
