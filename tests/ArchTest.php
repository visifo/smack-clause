<?php declare(strict_types=1);

use Visifo\SmackClause\Smack;

arch()->preset()->php()
    ->ignoring(Smack::class);

arch()->preset()->security();

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed();
