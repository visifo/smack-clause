<?php declare(strict_types=1);

namespace Visifo\SmackClause\Extensions;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class SmackMethod
{
    public function __construct(
        public string $name,
    ) {}
}
