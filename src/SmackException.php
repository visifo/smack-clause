<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use InvalidArgumentException;
use SplFileObject;

class SmackException extends InvalidArgumentException
{
    public static function forNullValue(array $origin): self
    {
        $subject = self::resolveSubject($origin);

        return new self(sprintf('Validation failed for `%s`: expected non-null value, got `null`.', $subject));
    }

    public static function forExpectedType(
        string $expectedType,
        mixed $actualValue,
        array $origin,
    ): self {
        $subject = self::resolveSubject($origin);

        return new self(sprintf(
            'Validation failed for `%s`: expected `%s`, got `%s`.',
            $subject,
            $expectedType,
            self::formatValue($actualValue),
        ));
    }

    public static function forConstraint(
        string $constraint,
        mixed $actualValue,
        array $origin,
    ): self {
        $subject = self::resolveSubject($origin);

        return new self(sprintf(
            'Validation failed for `%s`: expected `%s`, got `%s`.',
            $subject,
            $constraint,
            self::formatValue($actualValue),
        ));
    }

    private static function resolveSubject(array $origin): string
    {
        if (count($origin) == 0) {
            return 'Argument';
        }

        $_file = new SplFileObject($origin['file']);
        $_file->seek($origin['line'] - 1);
        $line = $_file->current();
        $_file = null;

        $output = [];
        preg_match("/{$origin['function']}\((.*?)\)/", $line, $output);

        return count($output) > 1 ? $output[1] : 'Argument';
    }

    private static function formatValue(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_string($value)) {
            return sprintf('"%s"', $value);
        }

        if (is_array($value)) {
            return sprintf('array(%d)', count($value));
        }

        if (is_object($value)) {
            return sprintf('object(%s)', $value::class);
        }

        if (is_resource($value)) {
            return sprintf('resource(%s)', get_resource_type($value));
        }

        return gettype($value);
    }
}
