<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use BadMethodCallException;

readonly class Smack
{
    public function __construct(
        private mixed $value,
        private Trace $trace,
    ) {}

    public static function that(mixed $value): Smack
    {
        $frames = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $frame = [];
        if (isset($frames[0]) && is_array($frames[0])) {
            $frame = $frames[0];
        }

        $trace = Trace::fromBacktrace($frame);

        if ($value !== null) {
            return new Smack($value, $trace);
        }

        throw SmackException::forNullValue($trace);
    }

    /**
     * @param callable(mixed, Trace, mixed...): mixed $resolver
     */
    public static function register(string $name, callable $resolver): void
    {
        self::registry()->register($name, $resolver);
    }

    public static function registerProvider(SmackProviderInterface $provider): void
    {
        $provider->register(self::registry());
    }

    /**
     * @param array<array-key, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        $resolver = self::registry()->resolve($name);
        if ($resolver === null) {
            throw new BadMethodCallException(sprintf('Smack method `%s` is not registered.', $name));
        }

        return $resolver($this->value, $this->trace, ...$arguments);
    }

    public function isBool(): BoolSmack
    {
        if (is_bool($this->value)) {
            return new BoolSmack($this->value, $this->trace);
        }

        throw SmackException::forExpectedType('bool', $this->value, $this->trace);
    }

    public function isString(): StringSmack
    {
        if (is_string($this->value)) {
            return new StringSmack($this->value, $this->trace);
        }

        throw SmackException::forExpectedType('string', $this->value, $this->trace);
    }

    public function isInt(): IntSmack
    {
        if (is_int($this->value)) {
            return new IntSmack($this->value, $this->trace);
        }

        throw SmackException::forExpectedType('int', $this->value, $this->trace);
    }

    public function isFloat(): FloatSmack
    {
        if (is_float($this->value)) {
            return new FloatSmack($this->value, $this->trace);
        }

        throw SmackException::forExpectedType('float', $this->value, $this->trace);
    }

    public function isObject(): ObjectSmack
    {
        if (is_object($this->value)) {
            return new ObjectSmack($this->value, $this->trace);
        }

        throw SmackException::forExpectedType('object', $this->value, $this->trace);
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
