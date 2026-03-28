<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class StringSmack implements Smackable
{
    public function __construct(
        private ?string $value,
        private Trace $trace,
        private bool $optional = false,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace, bool $optional = false): self
    {
        if ($value === null) {
            return new self(null, $trace, $optional);
        }

        if (is_string($value)) {
            return new self($value, $trace, $optional);
        }

        throw SmackException::forExpectedType('string', $value, $trace);
    }

    public function isNotEmpty(): self
    {
        if ($this->value === null) {
            if ($this->optional) {
                return $this;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if ($this->value !== '') {
            return $this;
        }

        throw SmackException::forConstraint('not empty', $this->value, $this->trace);
    }

    public function isNotBlank(): self
    {
        if ($this->value === null) {
            if ($this->optional) {
                return $this;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if (mb_trim($this->value) !== '') {
            return $this;
        }

        throw SmackException::forConstraint('not blank', $this->value, $this->trace);
    }
}
