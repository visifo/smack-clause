<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use InvalidArgumentException;

final class SmackRegistry
{
    /**
     * @var array<string, callable(mixed, Trace, mixed...): mixed>
     */
    private array $methods = [];

    public function register(string $name, callable $resolver): void
    {
        if ($name === '') {
            throw new InvalidArgumentException('Smack method name must not be empty.');
        }

        if (method_exists(Smack::class, $name)) {
            throw new InvalidArgumentException(sprintf('Smack method `%s` is reserved.', $name));
        }

        if (isset($this->methods[$name])) {
            throw new InvalidArgumentException(sprintf('Smack method `%s` is already registered.', $name));
        }

        $this->methods[$name] = $resolver;
    }

    /**
     * @return callable(mixed, Trace, mixed...): mixed|null
     */
    public function resolve(string $name): ?callable
    {
        return $this->methods[$name] ?? null;
    }
}
