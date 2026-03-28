<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RuntimeException;
use SplFileInfo;
use Throwable;
use Visifo\SmackClause\Extensions\SmackMethod;
use Visifo\SmackClause\Extensions\SmackRegistry;

final class IdeHelperCommand
{
    /**
     * @param array<int, string> $argv
     */
    public static function run(array $argv): int
    {
        try {
            $config = self::parseArguments($argv);
            $root = self::resolveRoot($config['root']);

            $autoloadPath = $root.'/vendor/autoload.php';
            if (! is_file($autoloadPath)) {
                throw new RuntimeException(sprintf('Could not find autoloader at `%s`.', $autoloadPath));
            }

            require_once $autoloadPath;

            $autoloadPaths = self::resolveAutoloadPaths($root);
            $scanPath = self::resolveScanPath($root, $config['scan']);
            $classFiles = self::discoverClassFiles($autoloadPaths, $scanPath);

            $registry = new SmackRegistry;
            foreach ($classFiles as $class => $file) {
                if (! class_exists($class)) {
                    require_once $file;
                }

                if (! class_exists($class)) {
                    if (interface_exists($class)) {
                        continue;
                    }

                    if (trait_exists($class)) {
                        continue;
                    }

                    if (enum_exists($class)) {
                        continue;
                    }

                    throw new RuntimeException(sprintf('Class `%s` from `%s` could not be loaded.', $class, $file));
                }

                if (! is_subclass_of($class, Smackable::class)) {
                    continue;
                }

                $reflection = new ReflectionClass($class);
                if ($reflection->isAbstract()) {
                    continue;
                }

                if ($reflection->getAttributes(SmackMethod::class) === []) {
                    continue;
                }

                $registry->register($class);
            }

            $methods = $registry->all();
            $content = self::buildHelperContent($methods);

            $outputFile = $root.'/_smack_ide_helper.php';
            if (file_put_contents($outputFile, $content) === false) {
                throw new RuntimeException(sprintf('Could not write helper file to `%s`.', $outputFile));
            }

            fwrite(STDOUT, sprintf(
                "Generated %d method annotation(s) in %s\n",
                count($methods),
                $outputFile,
            ));

            return 0;
        } catch (Throwable $throwable) {
            fwrite(STDERR, sprintf("smack-ide-helper failed: %s\n", $throwable->getMessage()));

            return 1;
        }
    }

    /**
     * @param array<int, string> $argv
     * @return array{root:string,scan:string|null}
     */
    private static function parseArguments(array $argv): array
    {
        $root = getcwd();
        $scanPath = null;

        foreach ($argv as $argument) {
            if (str_starts_with($argument, '--root=')) {
                $root = substr($argument, 7);
                continue;
            }

            if (str_starts_with($argument, '--scan=')) {
                if ($scanPath !== null) {
                    throw new RuntimeException('Only one `--scan` option is allowed.');
                }

                $scanPath = trim(substr($argument, 7));
                if ($scanPath === '') {
                    throw new RuntimeException('The `--scan` option must not be empty.');
                }
            }
        }

        if ($root === false) {
            throw new RuntimeException('Could not resolve current working directory.');
        }

        return [
            'root' => $root,
            'scan' => $scanPath,
        ];
    }

    private static function resolveRoot(string $root): string
    {
        $resolved = realpath($root);
        if ($resolved === false) {
            throw new RuntimeException(sprintf('Project root `%s` does not exist.', $root));
        }

        if (! is_dir($resolved)) {
            throw new RuntimeException(sprintf('Project root `%s` is not a directory.', $resolved));
        }

        return $resolved;
    }

    /**
     * @return array<string, list<string>>
     */
    private static function resolveAutoloadPaths(string $root): array
    {
        $composerPath = $root.'/composer.json';
        $composerContent = file_get_contents($composerPath);
        if (! is_string($composerContent)) {
            throw new RuntimeException(sprintf('Could not read composer.json at `%s`.', $composerPath));
        }

        /** @var array{autoload?: array{psr-4?: array<string, string|list<string>>}}|null $composer */
        $composer = json_decode($composerContent, true);
        if (! is_array($composer)) {
            throw new RuntimeException(sprintf('Could not decode composer.json at `%s`.', $composerPath));
        }

        $autoload = $composer['autoload']['psr-4'] ?? null;
        if (! is_array($autoload) || $autoload === []) {
            throw new RuntimeException('No autoload.psr-4 directories found in composer.json.');
        }

        $paths = [];
        foreach ($autoload as $prefix => $autoloadPaths) {
            $directories = is_array($autoloadPaths) ? $autoloadPaths : [$autoloadPaths];
            $resolvedDirectories = [];

            foreach ($directories as $autoloadDir) {
                if ($autoloadDir === '') {
                    continue;
                }

                $resolvedPath = self::normalizePath($root, $autoloadDir);
                if (! is_dir($resolvedPath)) {
                    continue;
                }

                $resolvedDirectories[] = $resolvedPath;
            }

            if ($resolvedDirectories === []) {
                continue;
            }

            $paths[$prefix] = array_values(array_unique($resolvedDirectories));
        }

        if ($paths === []) {
            throw new RuntimeException('No autoload.psr-4 directories from composer.json could be resolved.');
        }

        return $paths;
    }

    private static function normalizePath(string $root, string $path): string
    {
        $normalizedPath = str_starts_with($path, '/')
            ? rtrim($path, '/')
            : rtrim($root.'/'.trim($path, '/'), '/');

        $resolvedPath = realpath($normalizedPath);

        return is_string($resolvedPath) ? $resolvedPath : $normalizedPath;
    }

    private static function resolveScanPath(string $root, ?string $scanOverride): ?string
    {
        if ($scanOverride === null) {
            return null;
        }

        $path = self::normalizePath($root, $scanOverride);
        if (! is_dir($path)) {
            throw new RuntimeException(sprintf('Scan directory `%s` does not exist.', $path));
        }

        return $path;
    }

    /**
     * @param array<string, list<string>> $autoloadPaths
     * @return array<string, string>
     */
    private static function discoverClassFiles(array $autoloadPaths, ?string $scanPath): array
    {
        $classFiles = [];
        $matchedScanPath = $scanPath === null;

        foreach ($autoloadPaths as $prefix => $prefixPaths) {
            foreach ($prefixPaths as $basePath) {
                if ($scanPath !== null && ! self::isPathWithinDirectory($scanPath, $basePath)) {
                    continue;
                }

                $matchedScanPath = true;
                $directory = $scanPath ?? $basePath;
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
                );

                foreach ($iterator as $fileInfo) {
                    if (! $fileInfo instanceof SplFileInfo) {
                        continue;
                    }

                    if ($fileInfo->getExtension() !== 'php') {
                        continue;
                    }

                    $path = $fileInfo->getPathname();
                    $relativePath = substr($path, strlen($basePath) + 1);
                    if (! str_ends_with($relativePath, '.php')) {
                        continue;
                    }

                    $classSuffix = substr($relativePath, 0, -4);
                    $class = $prefix.str_replace(['/', '\\'], '\\', $classSuffix);
                    if (isset($classFiles[$class])) {
                        throw new RuntimeException(sprintf(
                            'Class `%s` is declared in multiple files: `%s` and `%s`.',
                            $class,
                            $classFiles[$class],
                            $path,
                        ));
                    }

                    $classFiles[$class] = $path;
                }
            }
        }

        if (! $matchedScanPath && $scanPath !== null) {
            throw new RuntimeException(sprintf(
                'Scan directory `%s` is not inside any root autoload.psr-4 directory.',
                $scanPath,
            ));
        }

        return $classFiles;
    }

    private static function isPathWithinDirectory(string $path, string $directory): bool
    {
        return $path === $directory || str_starts_with($path, $directory.'/');
    }

    /**
     * @param array<string, class-string<Smackable>> $methods
     */
    private static function buildHelperContent(array $methods): string
    {
        ksort($methods);

        $lines = [
            '<?php declare(strict_types=1);',
            '',
            '// This file is auto-generated by `vendor/bin/smack-ide-helper`.',
            '// Do not edit manually.',
            '',
            'namespace Visifo\\SmackClause;',
            '',
            '/**',
        ];

        foreach ($methods as $method => $class) {
            $lines[] = sprintf(' * @method \\%s %s()', ltrim($class, '\\'), $method);
        }

        $lines[] = ' */';
        $lines[] = 'final class IdeHelperSmack {}';
        $lines[] = '';

        return implode(PHP_EOL, $lines);
    }
}
