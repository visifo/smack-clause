<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class BoolSmack implements Smackable
{
    public function __construct(
        private bool $value,
        private Trace $trace,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace): self
    {
        if (! is_bool($value)) {
            throw SmackException::forExpectedType('bool', $value, $trace);
        }

        return new self($value, $trace);
    }

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
