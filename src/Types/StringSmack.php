<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class StringSmack implements Smackable
{
    public function __construct(
        private string $value,
        private Trace $trace,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace): self
    {
        if (! is_string($value)) {
            throw SmackException::forExpectedType('string', $value, $trace);
        }

        return new self($value, $trace);
    }

    public function isNotEmpty(): self
    {
        if ($this->value !== '') {
            return $this;
        }

        throw SmackException::forConstraint('not empty', $this->value, $this->trace);
    }

    public function isNotBlank(): self
    {
        if (mb_trim($this->value) !== '') {
            return $this;
        }

        throw SmackException::forConstraint('not blank', $this->value, $this->trace);
    }
}
