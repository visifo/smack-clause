<?php declare(strict_types=1);

namespace Visifo\SmackClause;

class BoolSmack
{
    public function __construct(
        private readonly bool $value,
    ) {}

    public function isTrue(): void
    {
        if ($this->value) {
            return;
        }

        throw new SmackViolation;
    }

    public function isFalse(): void
    {
        if (! $this->value) {
            return;
        }

        throw new SmackViolation;
    }
}
