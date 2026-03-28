<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class IntSmack implements Smackable
{
    public function __construct(
        private ?int $value,
        private Trace $trace,
        private bool $optional = false,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace, bool $optional = false): self
    {
        if ($value === null) {
            return new self(null, $trace, $optional);
        }

        if (is_int($value)) {
            return new self($value, $trace, $optional);
        }

        throw SmackException::forExpectedType('int', $value, $trace);
    }

    public function isPositive(): self
    {
        if ($this->value === null) {
            if ($this->optional) {
                return $this;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if ($this->value > 0) {
            return $this;
        }

        throw SmackException::forConstraint('positive int', $this->value, $this->trace);
    }

    public function isNegative(): self
    {
        if ($this->value === null) {
            if ($this->optional) {
                return $this;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if ($this->value < 0) {
            return $this;
        }

        throw SmackException::forConstraint('negative int', $this->value, $this->trace);
    }
}
