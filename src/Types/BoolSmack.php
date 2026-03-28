<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class BoolSmack implements Smackable
{
    public function __construct(
        private ?bool $value,
        private Trace $trace,
        private bool $optional = false,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace, bool $optional = false): self
    {
        if ($value === null) {
            return new self(null, $trace, $optional);
        }

        if (is_bool($value)) {
            return new self($value, $trace, $optional);
        }

        throw SmackException::forExpectedType('bool', $value, $trace);
    }

    public function isTrue(): void
    {
        if ($this->value === null) {
            if ($this->optional) {
                return;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if ($this->value) {
            return;
        }

        throw SmackException::forConstraint('true', $this->value, $this->trace);
    }

    public function isFalse(): void
    {
        if ($this->value === null) {
            if ($this->optional) {
                return;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if (! $this->value) {
            return;
        }

        throw SmackException::forConstraint('false', $this->value, $this->trace);
    }
}
