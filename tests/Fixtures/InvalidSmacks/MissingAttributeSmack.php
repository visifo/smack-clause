<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\InvalidSmacks;

use Override;
use Visifo\SmackClause\CustomSmack;
use Visifo\SmackClause\SmackException;
use Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer;
use Visifo\SmackClause\Trace;

final readonly class MissingAttributeSmack extends CustomSmack
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
