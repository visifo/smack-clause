<?php declare(strict_types=1);

namespace Visifo\SmackClause;

abstract readonly class CustomSmack
{
    public function __construct(
        protected Trace $trace,
    ) {}

    final protected function ensure(bool $condition, string $constraint, mixed $actualValue): void
    {
        if ($condition) {
            return;
        }

        throw SmackException::forConstraint($constraint, $actualValue, $this->trace);
    }

    final protected function failExpectedType(string $expectedType, mixed $actualValue): never
    {
        throw SmackException::forExpectedType($expectedType, $actualValue, $this->trace);
    }
}
