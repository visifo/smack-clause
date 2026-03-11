<?php declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use Visifo\SmackClause\Extensions\SmackRegistry;
use Visifo\SmackClause\IdeHelperCommand;

function ideHelperFixturePath(string $fixture): string
{
    $path = realpath(__DIR__.'/../Fixtures/'.$fixture);
    if ($path === false) {
        throw new RuntimeException(sprintf('Unable to resolve `%s` fixture path.', $fixture));
    }

    return $path;
}

describe('ide helper command', function (): void {
    it('generates helper file for valid custom smacks', function (): void {
        $validScanPath = ideHelperFixturePath('Smacks');
        $root = smackCreateTempProjectRoot([
            'Visifo\\SmackClause\\Tests\\Fixtures\\Smacks\\' => $validScanPath,
        ]);

        try {
            $exitCode = IdeHelperCommand::run([
                '--root='.$root,
                '--scan='.$validScanPath,
            ]);

            expect($exitCode)->toBe(0);

            $helperFile = $root.'/_smack_ide_helper.php';
            expect($helperFile)->toBeFile();

            $content = file_get_contents($helperFile);
            expect($content)
                ->toBeString()
                ->toContain('final class IdeHelperSmack {}')
                ->toContain(
                    '@method \\Visifo\\SmackClause\\Tests\\Fixtures\\Smacks\\PlayerSmack isPlayer()',
                );
        } finally {
            smackDeleteDirectory($root);
        }
    });

    it('fails when scan override paths are invalid', function (): void {
        $validScanPath = ideHelperFixturePath('Smacks');
        $root = smackCreateTempProjectRoot([
            'Visifo\\SmackClause\\Tests\\Fixtures\\Smacks\\' => $validScanPath,
        ]);

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

    it('fails when multiple scan override paths are provided', function (): void {
        $validScanPath = ideHelperFixturePath('Smacks');
        $root = smackCreateTempProjectRoot([
            'Visifo\\SmackClause\\Tests\\Fixtures\\Smacks\\' => $validScanPath,
        ]);

        try {
            $exitCode = IdeHelperCommand::run([
                '--root='.$root,
                '--scan='.$validScanPath,
                '--scan='.$validScanPath,
            ]);

            expect($exitCode)->toBe(1);
            expect(is_file($root.'/_smack_ide_helper.php'))->toBeFalse();
        } finally {
            smackDeleteDirectory($root);
        }
    });

    it('fails when scan path is outside the root autoload psr-4 directories', function (): void {
        $validScanPath = ideHelperFixturePath('Smacks');
        $invalidScanPath = ideHelperFixturePath('InvalidSmacks');
        $root = smackCreateTempProjectRoot([
            'Visifo\\SmackClause\\Tests\\Fixtures\\Smacks\\' => $validScanPath,
        ]);

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

    it('fails strictly for invalid custom smacks', function (): void {
        $invalidScanPath = ideHelperFixturePath('InvalidSmacks');
        $root = smackCreateTempProjectRoot([
            'Visifo\\SmackClause\\Tests\\Fixtures\\InvalidSmacks\\' => $invalidScanPath,
        ]);

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
        $validScanPath = ideHelperFixturePath('Smacks');
        $root = smackCreateTempProjectRoot([
            'Visifo\\SmackClause\\Tests\\Fixtures\\Smacks\\' => $validScanPath,
        ]);

        $loader = array_first(ClassLoader::getRegisteredLoaders());
        expect($loader)->toBeInstanceOf(ClassLoader::class);
        expect(class_exists(SmackRegistry::class))->toBeTrue();

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
            expect($root.'/_smack_ide_helper.php')->toBeFile();
        } finally {
            $loader->setClassMapAuthoritative($previous);
            smackDeleteDirectory($root);
        }
    });
});
