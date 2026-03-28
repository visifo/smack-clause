<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\InvalidSmacks;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Smackable;
use Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer;

final readonly class MissingAttributeSmack implements Smackable
{
    private function __construct() {}

    #[Override]
    public static function screenInto(mixed $value, Trace $trace, bool $optional = false): self
    {
        if ($value instanceof GamePlayer) {
            return new self;
        }

        throw SmackException::forExpectedType(GamePlayer::class, $value, $trace);
    }
}
