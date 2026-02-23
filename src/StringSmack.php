<?php declare(strict_types=1);

namespace Visifo\SmackClause;

class StringSmack
{
    public function __construct(
        private readonly string $value,
    ) {}

    public function isNotEmpty(): self
    {
        if ($this->value !== '') {
            return $this;
        }

        throw new SmackViolation;
    }

    public function isNotBlank(): self
    {
        if (mb_trim($this->value) !== '') {
            return $this;
        }

        throw new SmackViolation;
    }
}
