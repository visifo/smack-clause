<?php declare(strict_types=1);

namespace Visifo\SmackClause\Examples;

use Visifo\SmackClause\CustomSmack;
use Visifo\SmackClause\Smack;
use Visifo\SmackClause\SmackException;
use Visifo\SmackClause\SmackMethod;
use Visifo\SmackClause\Trace;

#[SmackMethod('isPlayer')]
final readonly class PlayerSmack extends CustomSmack
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

readonly class GamePlayer
{
    public function __construct(
        private bool $un,
        public Game $game,
    ) {}

    public function isUn(): bool
    {
        return $this->un;
    }
}

readonly class Game
{
    public function __construct(
        private bool $inPlayState,
    ) {}

    public function isInPlayState(): bool
    {
        return $this->inPlayState;
    }
}

// Example usage:
Smack::register(PlayerSmack::class);

$player = new GamePlayer(un: false, game: new Game(inPlayState: true));

Smack::that($player)
    ->isPlayer()
    ->isNotUn()
    ->isInPlayState();
