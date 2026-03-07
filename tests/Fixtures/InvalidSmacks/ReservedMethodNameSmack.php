<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\InvalidSmacks;

use Visifo\SmackClause\CustomSmack;
use Visifo\SmackClause\SmackException;
use Visifo\SmackClause\SmackMethod;
use Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer;
use Visifo\SmackClause\Trace;

#[SmackMethod('isString')]
final readonly class ReservedMethodNameSmack extends CustomSmack
{
    public function __construct(
        private GamePlayer $player,
        Trace $trace,
    ) {
        parent::__construct($trace);
    }

    public static function fromSmack(
        mixed $value,
        Trace $trace,
        mixed ...$arguments,
    ): static {
        if (! $value instanceof GamePlayer) {
            throw SmackException::forExpectedType(GamePlayer::class, $value, $trace);
        }

        return new self($value, $trace);
    }
}
