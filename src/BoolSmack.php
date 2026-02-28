<?php declare(strict_types=1);

namespace Visifo\SmackClause;

readonly class BoolSmack
{
    public function __construct(
        private bool $value,
        private array $origin,
    ) {}

    public function isTrue(): void
    {
        if ($this->value) {
            return;
        }

        throw SmackException::forConstraint('true', $this->value, $this->origin);
    }

    public function isFalse(): void
    {
        if (! $this->value) {
            return;
        }

        throw SmackException::forConstraint('false', $this->value, $this->origin);
    }
}
