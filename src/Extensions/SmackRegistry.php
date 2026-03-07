<?php declare(strict_types=1);

namespace Visifo\SmackClause\Extensions;

use InvalidArgumentException;
use ReflectionClass;
use Visifo\SmackClause\Smack;

final class SmackRegistry
{
    /**
     * @var array<string, class-string<CustomSmack>>
     */
    private array $methods = [];

    public function register(string $smackClass): void
    {
        if (! class_exists($smackClass)) {
            throw new InvalidArgumentException(sprintf('Smack class `%s` does not exist.', $smackClass));
        }

        if (! is_subclass_of($smackClass, CustomSmack::class)) {
            throw new InvalidArgumentException(sprintf(
                'Smack class `%s` must extend `%s`.',
                $smackClass,
                CustomSmack::class,
            ));
        }

        $reflection = new ReflectionClass($smackClass);
        $attributes = $reflection->getAttributes(SmackMethod::class);
        if ($attributes === []) {
            throw new InvalidArgumentException(sprintf(
                'Smack class `%s` must declare `#[SmackMethod(\"...\")]`.',
                $smackClass,
            ));
        }

        if (count($attributes) > 1) {
            throw new InvalidArgumentException(sprintf(
                'Smack class `%s` must declare exactly one `#[SmackMethod(\"...\")]` attribute.',
                $smackClass,
            ));
        }

        $name = $attributes[0]->newInstance()->name;

        if ($name === '') {
            throw new InvalidArgumentException('Smack method name must not be empty.');
        }

        if (! preg_match('/^[a-zA-Z_]\w*$/', $name)) {
            throw new InvalidArgumentException(sprintf('Smack method `%s` is not a valid PHP method name.', $name));
        }

        if (method_exists(Smack::class, $name)) {
            throw new InvalidArgumentException(sprintf('Smack method `%s` is reserved.', $name));
        }

        if (isset($this->methods[$name])) {
            throw new InvalidArgumentException(sprintf('Smack method `%s` is already registered.', $name));
        }

        if (! $reflection->hasMethod('fromSmack')) {
            throw new InvalidArgumentException(sprintf(
                'Smack class `%s` must define a public static `fromSmack` method.',
                $smackClass,
            ));
        }

        $constructor = $reflection->getMethod('fromSmack');
        if (! $constructor->isPublic() || ! $constructor->isStatic()) {
            throw new InvalidArgumentException(sprintf(
                'Smack class `%s` must define a public static `fromSmack` method.',
                $smackClass,
            ));
        }

        $this->methods[$name] = $smackClass;
    }

    /**
     * @return class-string<CustomSmack>|null
     */
    public function resolve(string $name): ?string
    {
        return $this->methods[$name] ?? null;
    }

    /**
     * @return array<string, class-string<CustomSmack>>
     */
    public function all(): array
    {
        return $this->methods;
    }
}
