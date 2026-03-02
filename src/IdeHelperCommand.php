<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use Composer\Autoload\ClassLoader;
use ReflectionClass;
use RuntimeException;
use Throwable;

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

            $loader = self::resolveClassLoader($root);
            $scanPaths = self::resolveScanPaths($root, $config['scan'], $loader);
            $classFiles = self::discoverClassFiles($scanPaths, $loader);

            $registry = new SmackRegistry;
            foreach ($classFiles as $class => $file) {
                if (! class_exists($class)) {
                    require_once $file;
                }

                if (! class_exists($class)) {
                    throw new RuntimeException(sprintf('Class `%s` from `%s` could not be loaded.', $class, $file));
                }

                if (! is_subclass_of($class, CustomSmack::class)) {
                    continue;
                }

                $reflection = new ReflectionClass($class);
                if ($reflection->isAbstract()) {
                    continue;
                }

                $registry->register($class);
            }

            $methods = $registry->all();
            $content = self::buildHelperContent($methods);

            $outputFile = $root.'/_smack_ide_helper.php';
            file_put_contents($outputFile, $content);

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
     * @return array{root:string,scan:list<string>}
     */
    private static function parseArguments(array $argv): array
    {
        $root = getcwd();
        $scanPaths = [];

        foreach ($argv as $argument) {
            if (str_starts_with($argument, '--root=')) {
                $root = substr($argument, 7);
                continue;
            }

            if (str_starts_with($argument, '--scan=')) {
                $scanPath = trim(substr($argument, 7));
                if ($scanPath !== '') {
                    $scanPaths[] = $scanPath;
                }
            }
        }

        if ($root === false) {
            throw new RuntimeException('Could not resolve current working directory.');
        }

        return [
            'root' => $root,
            'scan' => array_values(array_unique($scanPaths)),
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
     * @param list<string> $scanOverrides
     * @return list<string>
     */
    private static function resolveScanPaths(string $root, array $scanOverrides, ClassLoader $loader): array
    {
        if ($scanOverrides !== []) {
            $paths = [];
            foreach ($scanOverrides as $scanOverride) {
                $path = self::normalizePath($root, $scanOverride);
                if (is_dir($path)) {
                    $paths[] = $path;
                }
            }

            if ($paths === []) {
                throw new RuntimeException('No valid scan directories were found for --scan options.');
            }

            return array_values(array_unique($paths));
        }

        $autoload = $loader->getPrefixesPsr4();

        $paths = [];
        foreach ($autoload as $autoloadPaths) {
            foreach ($autoloadPaths as $autoloadDir) {
                if ($autoloadDir === '') {
                    continue;
                }

                $path = str_starts_with($autoloadDir, '/')
                    ? rtrim($autoloadDir, '/')
                    : self::normalizePath($root, $autoloadDir);

                if (! is_dir($path)) {
                    continue;
                }

                $resolvedPath = realpath($path);
                $paths[] = is_string($resolvedPath) ? $resolvedPath : $path;
            }
        }

        if ($paths === []) {
            throw new RuntimeException('No autoload.psr-4 directories found to scan.');
        }

        return array_values(array_unique($paths));
    }

    private static function normalizePath(string $root, string $path): string
    {
        if (str_starts_with($path, '/')) {
            return rtrim($path, '/');
        }

        return rtrim($root.'/'.trim($path, '/'), '/');
    }

    /**
     * @param list<string> $scanPaths
     * @return array<class-string, string>
     */
    private static function discoverClassFiles(array $scanPaths, ClassLoader $loader): array
    {
        $classMap = $loader->getClassMap();

        $classFiles = [];
        foreach ($classMap as $class => $file) {
            if (! str_ends_with($file, '.php')) {
                continue;
            }

            $path = $file;
            $resolvedPath = realpath($path);
            $path = is_string($resolvedPath) ? $resolvedPath : $path;

            if (! self::isPathWithinScanPaths($path, $scanPaths)) {
                continue;
            }

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

        return $classFiles;
    }

    /**
     * @param list<string> $scanPaths
     */
    private static function isPathWithinScanPaths(string $path, array $scanPaths): bool
    {
        return array_any(
            $scanPaths,
            static fn ($scanPath): bool => str_starts_with($path, $scanPath.'/') || $path === $scanPath,
        );
    }

    private static function resolveClassLoader(string $root): ClassLoader
    {
        $loaders = ClassLoader::getRegisteredLoaders();
        if ($loaders === []) {
            throw new RuntimeException('Could not resolve Composer class loader.');
        }

        $rootPrefix = $root.'/vendor/';
        foreach ($loaders as $vendorDir => $loader) {
            if (! str_starts_with($vendorDir, $rootPrefix)) {
                continue;
            }

            return $loader;
        }

        return array_first($loaders);
    }

    /**
     * @param array<string, class-string<CustomSmack>> $methods
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
