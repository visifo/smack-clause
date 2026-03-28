<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\InvalidSmacks;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Extensions\SmackMethod;
use Visifo\SmackClause\Smackable;
use Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer;

#[SmackMethod('isString')]
final readonly class ReservedMethodNameSmack implements Smackable
{
    private function __construct(
        private GamePlayer $player,
    ) {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace): self
    {
        if (! $value instanceof GamePlayer) {
            throw SmackException::forExpectedType(GamePlayer::class, $value, $trace);
        }

        return new self($value);
    }
}
