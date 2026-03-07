<?php declare(strict_types=1);

use Visifo\SmackClause\Extension\SmackRegistry;
use Visifo\SmackClause\Tests\Fixtures\InvalidSmacks\DuplicatePlayerSmack;
use Visifo\SmackClause\Tests\Fixtures\InvalidSmacks\InvalidMethodNameSmack;
use Visifo\SmackClause\Tests\Fixtures\InvalidSmacks\MissingAttributeSmack;
use Visifo\SmackClause\Tests\Fixtures\InvalidSmacks\NonCustomSmack;
use Visifo\SmackClause\Tests\Fixtures\InvalidSmacks\ReservedMethodNameSmack;
use Visifo\SmackClause\Tests\Fixtures\Smacks\PlayerSmack;

describe('register', function (): void {
    it('registers valid custom smack', function (): void {
        $registry = new SmackRegistry;

        $registry->register(PlayerSmack::class);

        expect($registry->resolve('isPlayer'))->toBe(PlayerSmack::class);
    });

    it('throws when class does not exist', function (): void {
        $registry = new SmackRegistry;

        $registry->register('App\\Unknown\\Nope');
    })->throws(InvalidArgumentException::class, 'does not exist');

    it('throws when class does not extend custom smack', function (): void {
        $registry = new SmackRegistry;

        $registry->register(NonCustomSmack::class);
    })->throws(InvalidArgumentException::class, 'must extend');

    it('throws when attribute is missing', function (): void {
        $registry = new SmackRegistry;

        $registry->register(MissingAttributeSmack::class);
    })->throws(InvalidArgumentException::class, 'must declare');

    it('throws when method name is invalid', function (): void {
        $registry = new SmackRegistry;

        $registry->register(InvalidMethodNameSmack::class);
    })->throws(InvalidArgumentException::class, 'is not a valid PHP method name');

    it('throws when method name is reserved', function (): void {
        $registry = new SmackRegistry;

        $registry->register(ReservedMethodNameSmack::class);
    })->throws(InvalidArgumentException::class, 'is reserved');

    it('throws when method name is already registered', function (): void {
        $registry = new SmackRegistry;
        $registry->register(PlayerSmack::class);

        $registry->register(DuplicatePlayerSmack::class);
    })->throws(InvalidArgumentException::class, 'already registered');
});
