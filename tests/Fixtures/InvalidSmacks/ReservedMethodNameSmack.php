<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\InvalidSmacks;

use Override;
use Visifo\SmackClause\Exception\SmackException;
use Visifo\SmackClause\Exception\Trace;
use Visifo\SmackClause\Extension\CustomSmack;
use Visifo\SmackClause\Extension\SmackMethod;
use Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer;

#[SmackMethod('isString')]
final readonly class ReservedMethodNameSmack extends CustomSmack
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
