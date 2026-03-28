<?php declare(strict_types=1);

use Visifo\SmackClause\Smack;
use Visifo\SmackClause\Smackable;

arch()->preset()->php()->ignoring(Smack::class);

arch()->preset()->security();

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed();

arch('implements Smackable')
    ->expect('Visifo\SmackClause\Types')
    ->toImplement(Smackable::class)
    ->toHaveSuffix('Smack')
    ->classes->not->toBeFinal();
