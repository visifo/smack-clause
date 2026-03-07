<?php declare(strict_types=1);

namespace Visifo\SmackClause\Types;

use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;

readonly class ObjectSmack
{
    public function __construct(
        private mixed $value,
        private Trace $trace,
    ) {}

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
