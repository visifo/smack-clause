<?php declare(strict_types=1);

namespace Visifo\SmackClause;

class FloatSmack
{
    public function __construct(
        private readonly float $value,
    ) {}

    public function isPositive(): self
    {
        if ($this->value > 0) {
            return $this;
        }

        throw new SmackViolation;
    }

    public function isNegative(): self
    {
        if ($this->value < 0) {
            return $this;
        }

        throw new SmackViolation;
    }
}
