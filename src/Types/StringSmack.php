<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Visifo\SmackClause\Exception\SmackException;
use Visifo\SmackClause\Exception\Trace;

readonly class StringSmack
{
    public function __construct(
        private string $value,
        private Trace $trace,
    ) {}

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
