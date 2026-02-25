<?php declare(strict_types=1);

namespace Visifo\SmackClause;

readonly class StringSmack
{
    public function __construct(
        private string $value,
        private array  $origin,
    ) {}

    public function isNotEmpty(): self
    {
        if ($this->value !== '') {
            return $this;
        }

        throw SmackException::forConstraint('not empty', $this->value, $this->origin);
    }

    public function isNotBlank(): self
    {
        if (mb_trim($this->value) !== '') {
            return $this;
        }

        throw SmackException::forConstraint('not blank', $this->value, $this->origin);
    }
}
