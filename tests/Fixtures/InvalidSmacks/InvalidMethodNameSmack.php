<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\InvalidSmacks;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Extensions\CustomSmack;
use Visifo\SmackClause\Extensions\SmackMethod;
use Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer;

#[SmackMethod('is-player')]
final readonly class InvalidMethodNameSmack extends CustomSmack
{
    #[Override]
    public static function fromSmack(
        mixed $value,
        Trace $trace,
        mixed ...$arguments,
    ): static {
        if (! $value instanceof GamePlayer) {
            throw SmackException::forExpectedType(GamePlayer::class, $value, $trace);
        }

        return new self($trace);
    }
}
