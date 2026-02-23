<?php declare(strict_types=1);

namespace Visifo\SmackClause;

class ObjectSmack
{
    public function __construct(
        private readonly object $value,
    ) {}

    public function isInstanceOf(string $class): self
    {
        if ($this->value instanceof $class) {
            return $this;
        }

        throw new SmackException;
    }
}
