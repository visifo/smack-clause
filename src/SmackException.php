<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use InvalidArgumentException;
use SplFileObject;

class SmackException extends InvalidArgumentException
{
    public static function forNullValue(Trace $trace): self
    {
        $subject = self::resolveSubject($trace);

        return new self(sprintf('Validation failed for `%s`: expected non-null value, got `null`.', $subject));
    }

    public static function forExpectedType(
        string $expectedType,
        mixed $actualValue,
        Trace $trace,
    ): self {
        $subject = self::resolveSubject($trace);

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
        Trace $trace,
    ): self {
        $subject = self::resolveSubject($trace);

        return new self(sprintf(
            'Validation failed for `%s`: expected `%s`, got `%s`.',
            $subject,
            $constraint,
            self::formatValue($actualValue),
        ));
    }

    private static function resolveSubject(Trace $trace): string
    {
        if (! is_file($trace->file)) {
            return 'Argument';
        }

        $_file = new SplFileObject($trace->file);
        $_file->seek(max(0, $trace->line - 1));

        $line = $_file->current();
        if (! is_string($line)) {
            return 'Argument';
        }

        $output = [];
        $pattern = sprintf('/%s\((.*?)\)/', preg_quote($trace->function, '/'));
        preg_match($pattern, $line, $output);

        return $output[1] ?? 'Argument';
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
