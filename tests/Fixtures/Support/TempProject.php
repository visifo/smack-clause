<?php declare(strict_types=1);

/**
 * @param array<string, string|list<string>> $autoloadPaths
 */
function smackCreateTempProjectRoot(array $autoloadPaths = []): string
{
    $root = sys_get_temp_dir().'/smack-ide-helper-'.bin2hex(random_bytes(8));
    mkdir($root, 0o777, true);
    mkdir($root.'/vendor', 0o777, true);

    if ($autoloadPaths === []) {
        $autoloadPaths = smackFixtureAutoloadPaths();
    }

    $composer = [
        'autoload' => [
            'psr-4' => $autoloadPaths,
        ],
    ];

    $composerContent = json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (! is_string($composerContent)) {
        throw new RuntimeException('Could not encode composer.json test fixture.');
    }

    file_put_contents(
        $root.'/composer.json',
        $composerContent.PHP_EOL,
    );

    $autoload = realpath(__DIR__.'/../../../vendor/autoload.php');
    if (! is_string($autoload)) {
        throw new RuntimeException('Could not resolve vendor/autoload.php for tests.');
    }

    $autoloadProxy = sprintf("<?php require_once '%s';\n", addslashes($autoload));
    file_put_contents($root.'/vendor/autoload.php', $autoloadProxy);

    return $root;
}

/**
 * @return array<string, string>
 */
function smackFixtureAutoloadPaths(): array
{
    $smacksPath = realpath(__DIR__.'/../Smacks');
    $invalidSmacksPath = realpath(__DIR__.'/../InvalidSmacks');

    if (! is_string($smacksPath) || ! is_string($invalidSmacksPath)) {
        throw new RuntimeException('Could not resolve ide helper fixture directories.');
    }

    return [
        'Visifo\\SmackClause\\Tests\\Fixtures\\Smacks\\' => $smacksPath,
        'Visifo\\SmackClause\\Tests\\Fixtures\\InvalidSmacks\\' => $invalidSmacksPath,
    ];
}

function smackDeleteDirectory(string $path): void
{
    if (! is_dir($path)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST,
    );

    foreach ($iterator as $item) {
        if (! $item instanceof SplFileInfo) {
            continue;
        }

        if ($item->isDir()) {
            rmdir($item->getPathname());
            continue;
        }

        unlink($item->getPathname());
    }

    rmdir($path);
}
