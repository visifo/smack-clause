<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class FloatSmack implements Smackable
{
    public function __construct(
        private ?float $value,
        private Trace $trace,
        private bool $optional = false,
        private bool $allowZero = false,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace, bool $optional = false): self
    {
        if ($value === null) {
            return new self(null, $trace, $optional);
        }

        if (is_float($value)) {
            return new self($value, $trace, $optional);
        }

        throw SmackException::forExpectedType('float', $value, $trace);
    }

    public function allowZero(): self
    {
        return new self(
            value: $this->value,
            trace: $this->trace,
            optional: $this->optional,
            allowZero: true,
        );
    }

    public function isPositive(): self
    {
        if ($this->value === null) {
            if ($this->optional) {
                return $this;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if ($this->allowZero && $this->value === 0.0) {
            return $this;
        }

        if ($this->value > 0) {
            return $this;
        }

        throw SmackException::forConstraint(
            $this->allowZero ? 'positive or zero float' : 'positive float',
            $this->value,
            $this->trace,
        );
    }

    public function isNegative(): self
    {
        if ($this->value === null) {
            if ($this->optional) {
                return $this;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if ($this->allowZero && $this->value === 0.0) {
            return $this;
        }

        if ($this->value < 0) {
            return $this;
        }

        throw SmackException::forConstraint(
            $this->allowZero ? 'negative or zero float' : 'negative float',
            $this->value,
            $this->trace,
        );
    }
}
