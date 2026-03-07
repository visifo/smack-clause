<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use Visifo\SmackClause\Exception\SmackException;
use Visifo\SmackClause\Exception\Trace;

readonly class IntSmack
{
    public function __construct(
        private int $value,
        private Trace $trace,
    ) {}

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
