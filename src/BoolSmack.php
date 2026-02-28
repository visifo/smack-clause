<?php declare(strict_types=1);

namespace Visifo\SmackClause;

readonly class BoolSmack
{
    public function __construct(
        private bool $value,
        private Trace $trace,
    ) {}

    public function isTrue(): void
    {
        if ($this->value) {
            return;
        }

        throw SmackException::forConstraint('true', $this->value, $this->trace);
    }

    public function isFalse(): void
    {
        if (! $this->value) {
            return;
        }

        throw SmackException::forConstraint('false', $this->value, $this->trace);
    }
}
