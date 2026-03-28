<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class ObjectSmack implements Smackable
{
    public function __construct(
        private ?object $value,
        private Trace $trace,
        private bool $optional = false,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace, bool $optional = false): self
    {
        if ($value === null) {
            return new self(null, $trace, $optional);
        }

        if (is_object($value)) {
            return new self($value, $trace, $optional);
        }

        throw SmackException::forExpectedType('object', $value, $trace);
    }

    /**
     * @param class-string $class
     */
    public function isInstanceOf(string $class): self
    {
        if ($this->value === null) {
            if ($this->optional) {
                return $this;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if ($this->value instanceof $class) {
            return $this;
        }

        throw SmackException::forConstraint('instance of', $this->value, $this->trace);
    }
}
