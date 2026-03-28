<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\Smacks;

use Override;
use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Exceptions\Trace;
use Visifo\SmackClause\Extensions\SmackMethod;
use Visifo\SmackClause\Types\ObjectSmack;

#[SmackMethod('isPlayer')]
final readonly class PlayerSmack extends ObjectSmack
{
    public function __construct(
        private ?GamePlayer $player,
        private Trace $trace,
        private bool $optional = false,
    ) {
        parent::__construct($player, $trace, $optional);
    }

    #[Override]
    public static function screenInto(mixed $value, Trace $trace, bool $optional = false): self
    {
        if ($value === null) {
            return new self(null, $trace, $optional);
        }

        if ($value instanceof GamePlayer) {
            return new self($value, $trace, $optional);
        }

        throw SmackException::forExpectedType(GamePlayer::class, $value, $trace);
    }

    public function isNotUn(): self
    {
        if (! $this->player instanceof GamePlayer) {
            if ($this->optional) {
                return $this;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if (! $this->player->isUn()) {
            return $this;
        }

        throw SmackException::forConstraint('not UN', $this->player, $this->trace);
    }

    public function isInPlayState(): self
    {
        if (! $this->player instanceof GamePlayer) {
            if ($this->optional) {
                return $this;
            }

            throw SmackException::forNullValue($this->trace);
        }

        if ($this->player->game->isInPlayState()) {
            return $this;
        }

        throw SmackException::forConstraint('game in play state', $this->player, $this->trace);
    }
}
