<?php declare(strict_types=1);

namespace Visifo\SmackClause;

readonly class Smack
{
    public function __construct(
        private mixed $value,
        private array $origin,
    ) {}

    public static function that(mixed $value): Smack
    {
        $origin = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0] ?? [];

        if ($value !== null) {
            return new Smack($value, $origin);
        }

        throw SmackException::forNullValue($origin);
    }

    public function isBool(): BoolSmack
    {
        if (is_bool($this->value)) {
            return new BoolSmack($this->value, $this->origin);
        }

        throw SmackException::forExpectedType('bool', $this->value, $this->origin);
    }

    public function isString(): StringSmack
    {
        if (is_string($this->value)) {
            return new StringSmack($this->value, $this->origin);
        }

        throw SmackException::forExpectedType('string', $this->value, $this->origin);
    }

    public function isInt(): IntSmack
    {
        if (is_int($this->value)) {
            return new IntSmack($this->value, $this->origin);
        }

        throw SmackException::forExpectedType('int', $this->value, $this->origin);
    }

    public function isFloat(): FloatSmack
    {
        if (is_float($this->value)) {
            return new FloatSmack($this->value, $this->origin);
        }

        throw SmackException::forExpectedType('float', $this->value, $this->origin);
    }

    public function isObject(): ObjectSmack
    {
        if (is_object($this->value)) {
            return new ObjectSmack($this->value, $this->origin);
        }

        throw SmackException::forExpectedType('object', $this->value, $this->origin);
    }
}
