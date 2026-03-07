<?php declare(strict_types=1);

namespace Visifo\SmackClause\Exception;

readonly class Trace
{
    private function __construct(
        public string $file,
        public int $line,
        public string $function,
    ) {}

    /**
     * @param array<array-key, mixed> $frame
     */
    public static function fromBacktrace(array $frame): self
    {
        $file = isset($frame['file']) && is_string($frame['file']) ? $frame['file'] : __FILE__;
        $line = isset($frame['line']) && is_int($frame['line']) ? $frame['line'] : 1;
        $function = isset($frame['function']) && is_string($frame['function']) ? $frame['function'] : 'unknown';

        return new self($file, $line, $function);
    }
}
