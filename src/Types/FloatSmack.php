<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;

readonly class FloatSmack
{
    public function __construct(
        private float $value,
        private Trace $trace,
    ) {}

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
