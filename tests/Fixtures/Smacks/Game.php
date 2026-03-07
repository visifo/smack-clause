<?php declare(strict_types=1);

namespace Visifo\SmackClause\Tests\Fixtures\Smacks;

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
