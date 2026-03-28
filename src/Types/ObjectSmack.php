<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;

readonly class ObjectSmack implements Smackable
{
    public function __construct(
        private object $value,
        private Trace $trace,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace): self
    {
        if (! is_object($value)) {
            throw SmackException::forExpectedType('object', $value, $trace);
        }

        return new self($value, $trace);
    }

    /**
     * @param class-string $class
     */
    public function isInstanceOf(string $class): self
    {
        if ($this->value instanceof $class) {
            return $this;
        }

        throw SmackException::forConstraint('instance of', $this->value, $this->trace);
    }
}
