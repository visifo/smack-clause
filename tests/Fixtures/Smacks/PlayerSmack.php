<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\Smacks;

use Override;
use Visifo\SmackClause\CustomSmack;
use Visifo\SmackClause\Exception\SmackException;
use Visifo\SmackClause\Exception\Trace;
use Visifo\SmackClause\SmackMethod;

#[SmackMethod('isPlayer')]
final readonly class PlayerSmack extends CustomSmack
{
    public function __construct(
        private GamePlayer $player,
        Trace $trace,
    ) {
        parent::__construct($trace);
    }

    #[Override]
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

    public function isNotUn(): self
    {
        $this->ensure(! $this->player->isUn(), 'not UN', $this->player);

        return $this;
    }

    public function isInPlayState(): self
    {
        $this->ensure($this->player->game->isInPlayState(), 'game in play state', $this->player);

        return $this;
    }
}
