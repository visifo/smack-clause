<?php declare(strict_types=1);

describe('smack-ide-helper bin', function (): void {
    it('runs command and generates helper file', function (): void {
        $root = smackCreateTempProjectRoot();
        $scanPath = realpath(__DIR__.'/../Fixtures/Smacks');

        try {
            expect($scanPath)->toBeString();

            $binary = realpath(__DIR__.'/../../bin/smack-ide-helper');
            expect($binary)->toBeString();

            if (! is_string($scanPath) || ! is_string($binary)) {
                return;
            }

            $command = sprintf(
                '%s %s --root=%s --scan=%s 2>&1',
                escapeshellarg(PHP_BINARY),
                escapeshellarg($binary),
                escapeshellarg($root),
                escapeshellarg($scanPath),
            );

            exec($command, $output, $exitCode);

            expect($exitCode)->toBe(0);
            expect(implode("\n", $output))->toContain('Generated 1 method annotation');
            expect(is_file($root.'/_smack_ide_helper.php'))->toBeTrue();
        } finally {
            smackDeleteDirectory($root);
        }
    });
});
