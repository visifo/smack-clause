<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\InvalidSmacks;

use Visifo\SmackClause\SmackMethod;
use Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer;

#[SmackMethod('isNonCustomSmack')]
final readonly class NonCustomSmack
{
    public function __construct(
        private GamePlayer $player,
    ) {}
}
