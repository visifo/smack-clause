<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class IntSmack implements Smackable
{
    public function __construct(
        private int $value,
        private Trace $trace,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace): self
    {
        if (is_int($value)) {
            return new self($value, $trace);
        }

        throw SmackException::forExpectedType('int', $value, $trace);
    }

    public function isPositive(): self
    {
        if ($this->value > 0) {
            return $this;
        }

        throw SmackException::forConstraint('positive int', $this->value, $this->trace);
    }

    public function isNegative(): self
    {
        if ($this->value < 0) {
            return $this;
        }

        throw SmackException::forConstraint('negative int', $this->value, $this->trace);
    }
}
