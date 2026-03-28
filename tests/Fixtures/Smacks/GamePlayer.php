<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\Smacks;

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
