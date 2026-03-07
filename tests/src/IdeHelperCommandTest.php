<?php declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use Visifo\SmackClause\IdeHelperCommand;

describe('ide helper command', function (): void {
    it('generates helper file for valid custom smacks', function (): void {
        $root = smackCreateTempProjectRoot();
        $validScanPath = realpath(__DIR__.'/../Fixtures/Smacks');
        expect($validScanPath)->toBeString();

        try {
            $exitCode = IdeHelperCommand::run([
                '--root='.$root,
                '--scan='.$validScanPath,
            ]);

            expect($exitCode)->toBe(0);

            $helperFile = $root.'/_smack_ide_helper.php';
            expect(is_file($helperFile))->toBeTrue();

            $content = file_get_contents($helperFile);
            expect($content)->toBeString();
            expect($content)->toContain('final class IdeHelperSmack {}');
            expect($content)->toContain('@method \\Visifo\\SmackClause\\Tests\\Fixtures\\Smacks\\PlayerSmack isPlayer()');
        } finally {
            smackDeleteDirectory($root);
        }
    });

    it('fails when scan override paths are invalid', function (): void {
        $root = smackCreateTempProjectRoot();

        try {
            $exitCode = IdeHelperCommand::run([
                '--root='.$root,
                '--scan=not-a-valid-path',
            ]);

            expect($exitCode)->toBe(1);
            expect(is_file($root.'/_smack_ide_helper.php'))->toBeFalse();
        } finally {
            smackDeleteDirectory($root);
        }
    });

    it('fails strictly for invalid custom smacks', function (): void {
        $root = smackCreateTempProjectRoot();
        $invalidScanPath = realpath(__DIR__.'/../Fixtures/InvalidSmacks');
        expect($invalidScanPath)->toBeString();

        try {
            $exitCode = IdeHelperCommand::run([
                '--root='.$root,
                '--scan='.$invalidScanPath,
            ]);

            expect($exitCode)->toBe(1);
            expect(is_file($root.'/_smack_ide_helper.php'))->toBeFalse();
        } finally {
            smackDeleteDirectory($root);
        }
    });

    it('works when class map is authoritative (optimized mode simulation)', function (): void {
        $root = smackCreateTempProjectRoot();
        $validScanPath = realpath(__DIR__.'/../Fixtures/Smacks');
        expect($validScanPath)->toBeString();

        $loader = array_first(ClassLoader::getRegisteredLoaders());
        expect($loader)->toBeInstanceOf(ClassLoader::class);

        if (! $loader instanceof ClassLoader) {
            smackDeleteDirectory($root);
            return;
        }

        $previous = $loader->isClassMapAuthoritative();
        $loader->setClassMapAuthoritative(true);

        try {
            $exitCode = IdeHelperCommand::run([
                '--root='.$root,
                '--scan='.$validScanPath,
            ]);

            expect($exitCode)->toBe(0);
            expect(is_file($root.'/_smack_ide_helper.php'))->toBeTrue();
        } finally {
            $loader->setClassMapAuthoritative($previous);
            smackDeleteDirectory($root);
        }
    });
});
