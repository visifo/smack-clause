<?php declare(strict_types=1);

function smackCreateTempProjectRoot(): string
{
    $root = sys_get_temp_dir().'/smack-ide-helper-'.bin2hex(random_bytes(8));
    mkdir($root, 0o777, true);
    mkdir($root.'/vendor', 0o777, true);

    $autoload = realpath(__DIR__.'/../../vendor/autoload.php');
    if (! is_string($autoload)) {
        throw new RuntimeException('Could not resolve vendor/autoload.php for tests.');
    }

    $autoloadProxy = sprintf("<?php require_once '%s';\n", addslashes($autoload));
    file_put_contents($root.'/vendor/autoload.php', $autoloadProxy);

    return $root;
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
