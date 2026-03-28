<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use BadMethodCallException;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Extensions\SmackRegistry;
use Visifo\SmackClause\Types\BoolSmack;
use Visifo\SmackClause\Types\FloatSmack;
use Visifo\SmackClause\Types\IntSmack;
use Visifo\SmackClause\Types\ObjectSmack;
use Visifo\SmackClause\Types\StringSmack;

/**
 * @mixin IdeHelperSmack
 */
readonly class Smack
{
    public function __construct(
        private mixed $value,
        private Trace $trace,
    ) {}

    public static function that(mixed $value): Smack
    {
        $frames = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $frame = $frames[0] ?? [];

        $trace = Trace::fromBacktrace($frame);

        if ($value !== null) {
            return new Smack($value, $trace);
        }

        throw SmackException::forNullValue($trace);
    }

    /**
     * @param class-string<Smackable> $smackClass
     */
    public static function register(string $smackClass): void
    {
        self::registry()->register($smackClass);
    }

    /**
     * @param array<array-key, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        $smackClass = self::registry()->resolve($name);
        if ($smackClass === null) {
            throw new BadMethodCallException(sprintf('Smack method `%s` is not registered.', $name));
        }

        if ($arguments !== []) {
            throw new BadMethodCallException(sprintf('Smack method `%s` does not accept arguments.', $name));
        }

        return $smackClass::screenInto($this->value, $this->trace);
    }

    public function isBool(): BoolSmack
    {
        return BoolSmack::screenInto($this->value, $this->trace);
    }

    public function isString(): StringSmack
    {
        return StringSmack::screenInto($this->value, $this->trace);
    }

    public function isInt(): IntSmack
    {
        return IntSmack::screenInto($this->value, $this->trace);
    }

    public function isFloat(): FloatSmack
    {
        return FloatSmack::screenInto($this->value, $this->trace);
    }

    public function isObject(): ObjectSmack
    {
        return ObjectSmack::screenInto($this->value, $this->trace);
    }

    private static function registry(): SmackRegistry
    {
        static $registry;

        if (! $registry instanceof SmackRegistry) {
            $registry = new SmackRegistry;
        }

        return $registry;
    }
}
