<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class FloatSmack implements Smackable
{
    public function __construct(
        private float $value,
        private Trace $trace,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace): self
    {
        if (is_float($value)) {
            return new self($value, $trace);
        }

        throw SmackException::forExpectedType('float', $value, $trace);
    }

    public function isPositive(): self
    {
        if ($this->value > 0) {
            return $this;
        }

        throw SmackException::forConstraint('positive float', $this->value, $this->trace);
    }

    public function isNegative(): self
    {
        if ($this->value < 0) {
            return $this;
        }

        throw SmackException::forConstraint('negative float', $this->value, $this->trace);
    }
}
