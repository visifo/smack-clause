<?php declare(strict_types=1);

namespace Visifo\SmackClause;

readonly class IntSmack
{
    public function __construct(
        private int   $value,
        private array $origin,
    ) {}

    public function isPositive(): self
    {
        if ($this->value > 0) {
            return $this;
        }

        throw SmackException::forConstraint('positive int', $this->value, $this->origin);
    }

    public function isNegative(): self
    {
        if ($this->value < 0) {
            return $this;
        }

        throw SmackException::forConstraint('negative int', $this->value, $this->origin);
    }
}
