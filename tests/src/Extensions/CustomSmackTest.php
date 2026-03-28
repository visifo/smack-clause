<?php declare(strict_types=1);

use Visifo\SmackClause\Exceptions\SmackException;
use Visifo\SmackClause\Smack;
use Visifo\SmackClause\Tests\Fixtures\Smacks\Game;
use Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer;
use Visifo\SmackClause\Tests\Fixtures\Smacks\PlayerSmack;

beforeAll(function (): void {
    Smack::register(PlayerSmack::class);
});

describe('dynamic smack registration', function (): void {
    describe('that', function (): void {
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

        it('throws for null value before dynamic screening', function (): void {
            Smack::that(null)->isPlayer();
        })->throws(SmackException::class, 'Validation failed for `null`: expected non-null value, got `null`.');

        it('throws for unregistered dynamic method', function (): void {
            Smack::that('value')->isUnknownSmackMethod();
        })->throws(BadMethodCallException::class, 'is not registered');

        it('throws when arguments are passed to dynamic methods', function (): void {
            $player = new GamePlayer(un: false, game: new Game(inPlayState: true));

            Smack::that($player)->isPlayer('argument');
        })->throws(BadMethodCallException::class, 'does not accept arguments');

        it('uses custom smack type checks and messages', function (): void {
            Smack::that('not a player')->isPlayer();
        })->throws(SmackException::class, 'expected `Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer`');
    });

    describe('maybe', function (): void {
        it('skips custom smack chain for null value', function (): void {
            $result = Smack::maybe(null)
                ->isPlayer()
                ->isNotUn()
                ->isInPlayState();

            expect($result)->toBeInstanceOf(PlayerSmack::class);
        });

        it('throws for unregistered dynamic method', function (): void {
            Smack::maybe('value')->isUnknownSmackMethod();
        })->throws(BadMethodCallException::class, 'is not registered');

        it('uses custom smack type checks and messages for non-null value', function (): void {
            Smack::maybe('not a player')->isPlayer();
        })->throws(SmackException::class, 'expected `Visifo\SmackClause\Tests\Fixtures\Smacks\GamePlayer`');
    });
});
