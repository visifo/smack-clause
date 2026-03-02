<?php declare(strict_types=1);

namespace Visifo\SmackClause;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RuntimeException;
use SplFileInfo;
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

            $scanPaths = self::resolveScanPaths($root, $config['scan']);
            $classFiles = self::discoverClassFiles($scanPaths);

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
    private static function resolveScanPaths(string $root, array $scanOverrides): array
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

        $composerFile = $root.'/composer.json';
        if (! is_file($composerFile)) {
            throw new RuntimeException(sprintf('Could not find composer.json in `%s`.', $root));
        }

        $composerJson = file_get_contents($composerFile);
        if (! is_string($composerJson)) {
            throw new RuntimeException('Could not read composer.json.');
        }

        $composer = json_decode($composerJson, true);
        if (! is_array($composer)) {
            throw new RuntimeException('composer.json is not valid JSON.');
        }

        $autoload = $composer['autoload']['psr-4'] ?? null;
        if (! is_array($autoload)) {
            throw new RuntimeException('composer.json must contain autoload.psr-4 mappings.');
        }

        $paths = [];
        foreach ($autoload as $prefix => $autoloadPaths) {
            if (! is_string($prefix)) {
                continue;
            }

            if (is_string($autoloadPaths)) {
                $autoloadPaths = [$autoloadPaths];
            }

            if (! is_array($autoloadPaths)) {
                continue;
            }

            foreach ($autoloadPaths as $autoloadPath) {
                if (! is_string($autoloadPath)) {
                    continue;
                }

                if ($autoloadPath === '') {
                    continue;
                }

                $path = self::normalizePath($root, $autoloadPath);
                if (is_dir($path)) {
                    $paths[] = $path;
                }
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
    private static function discoverClassFiles(array $scanPaths): array
    {
        $classFiles = [];

        foreach ($scanPaths as $scanPath) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($scanPath, FilesystemIterator::SKIP_DOTS),
            );

            foreach ($iterator as $fileInfo) {
                if (! $fileInfo instanceof SplFileInfo) {
                    continue;
                }

                if ($fileInfo->getExtension() !== 'php') {
                    continue;
                }

                $path = $fileInfo->getPathname();
                $class = self::extractClassName($path);
                if ($class === null) {
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
        }

        return $classFiles;
    }

    /**
     * @return class-string|null
     */
    private static function extractClassName(string $path): ?string
    {
        $code = file_get_contents($path);
        if (! is_string($code)) {
            throw new RuntimeException(sprintf('Could not read file `%s`.', $path));
        }

        $tokens = token_get_all($code);
        $namespace = '';
        $class = null;

        for ($i = 0, $count = count($tokens); $i < $count; $i++) {
            $token = $tokens[$i];
            if (! is_array($token)) {
                continue;
            }

            if ($token[0] === T_NAMESPACE) {
                $namespace = self::readNamespace($tokens, $i + 1);
                continue;
            }

            if ($token[0] !== T_CLASS) {
                continue;
            }

            $previous = self::previousToken($tokens, $i - 1);
            if ($previous !== null && in_array($previous, [T_DOUBLE_COLON, T_NEW], true)) {
                continue;
            }

            $class = self::readClassName($tokens, $i + 1);
            if ($class !== null) {
                break;
            }
        }

        if ($class === null) {
            return null;
        }

        return $namespace === '' ? $class : $namespace.'\\'.$class;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private static function readNamespace(array $tokens, int $index): string
    {
        $namespace = '';

        for ($i = $index, $count = count($tokens); $i < $count; $i++) {
            $token = $tokens[$i];
            if (! is_array($token)) {
                if ($token === ';' || $token === '{') {
                    break;
                }

                continue;
            }

            if (! in_array($token[0], [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED], true)) {
                continue;
            }

            $namespace .= $token[1];
        }

        return $namespace;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private static function readClassName(array $tokens, int $index): ?string
    {
        for ($i = $index, $count = count($tokens); $i < $count; $i++) {
            $token = $tokens[$i];
            if (! is_array($token)) {
                continue;
            }

            if ($token[0] === T_STRING) {
                return $token[1];
            }
        }

        return null;
    }

    /**
     * @param array<int, mixed> $tokens
     */
    private static function previousToken(array $tokens, int $index): ?int
    {
        for ($i = $index; $i >= 0; $i--) {
            $token = $tokens[$i];
            if (! is_array($token)) {
                continue;
            }

            if (in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            return $token[0];
        }

        return null;
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
