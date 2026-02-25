<?php declare(strict_types=1);

namespace Visifo\SmackClause;

readonly class FloatSmack
{
    public function __construct(
        private float $value,
        private array $origin,
    ) {}

    public function isPositive(): self
    {
        if ($this->value > 0) {
            return $this;
        }

        throw SmackException::forConstraint('positive float', $this->value, $this->origin);
    }

    public function isNegative(): self
    {
        if ($this->value < 0) {
            return $this;
        }

        throw SmackException::forConstraint('negative float', $this->value, $this->origin);
    }
}
