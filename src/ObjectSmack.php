<?php declare(strict_types=1);

namespace Visifo\SmackClause;

readonly class ObjectSmack
{
    public function __construct(
        private object $value,
        private array $origin,
    ) {}

    public function isInstanceOf(string $class): self
    {
        if ($this->value instanceof $class) {
            return $this;
        }

        throw SmackException::forConstraint('instance of', $this->value, $this->origin);
    }
}
