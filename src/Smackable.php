<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use Visifo\SmackClause\Exceptions\Trace;

interface Smackable
{
    public static function screenInto(mixed $value, Trace $trace, bool $optional = false): self;
}
