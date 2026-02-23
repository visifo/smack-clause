<?php declare(strict_types=1);

use Visifo\SmackClause\Smack;

it('can test', function (): void {
    expect(true)->toBeTrue();

    $bla = 35;
    Smack::that($bla)->isInt()->max(20);
});
