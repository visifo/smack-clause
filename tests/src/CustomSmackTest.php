<?php declare(strict_types=1);

use Visifo\SmackClause\Exception\SmackException;
use Visifo\SmackClause\Smack;
use Visifo\SmackClause\Tests\Fixtures\Smacks\Game;
use Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer;
use Visifo\SmackClause\Tests\Fixtures\Smacks\PlayerSmack;

beforeAll(function (): void {
    Smack::register(PlayerSmack::class);
});

describe('custom smack registration', function (): void {
    it('returns custom smack instance for registered method', function (): void {
        $player = new GamePlayer(un: false, game: new Game(inPlayState: true));

        $result = Smack::that($player)->isPlayer();

        expect($result)->toBeInstanceOf(PlayerSmack::class);
    });

    it('supports custom smack chaining', function (): void {
        $player = new GamePlayer(un: false, game: new Game(inPlayState: true));

        $result = Smack::that($player)
            ->isPlayer()
            ->isNotUn()
            ->isInPlayState();

        expect($result)->toBeInstanceOf(PlayerSmack::class);
    });

    it('throws for unregistered dynamic method', function (): void {
        Smack::that('value')->isUnknownSmackMethod();
    })->throws(BadMethodCallException::class, 'is not registered');

    it('uses custom smack type checks and messages', function (): void {
        Smack::that('not a player')->isPlayer();
    })->throws(SmackException::class, 'expected `Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer`');
});
