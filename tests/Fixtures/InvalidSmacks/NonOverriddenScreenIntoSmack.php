<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\InvalidSmacks;

use Visifo\SmackClause\Extensions\SmackMethod;
use Visifo\SmackClause\Types\ObjectSmack;

#[SmackMethod('isInheritedScreenInto')]
final readonly class NonOverriddenScreenIntoSmack extends ObjectSmack {}
