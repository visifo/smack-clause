# SmackClause 🥊

[![Latest Version on Packagist](https://img.shields.io/packagist/v/visifo/smackclause.svg?style=flat-square)](https://packagist.org/packages/visifo/smackclause)
[![Tests](https://img.shields.io/github/actions/workflow/status/visifo/smackclause/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/visifo/smackclause/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/visifo/smackclause.svg?style=flat-square)](https://packagist.org/packages/visifo/smackclause)

**Fail fast. Speak clear. Stop bad input early.**

SmackClause is a no-nonsense guard layer for PHP 8.5+. It hits bad input before it reaches your business logic, leaving a clean, structured trace of exactly what went wrong.


## Why Smack?
- **Direct Assertions**: No "should be" or "must be" fluff. Just `isInt()`, `isEmail()`, `min()`.
- **Auto-Discovery**: It knows your variable names. No more passing string labels manually.
- **PHP 8.5 Native**: Built for the future with property hooks, asymmetric visibility, and high-performance tokenization.
- **Smart Nullability**: Choose between a hard hit (`that()`) or a quiet pass (`maybe()`).

## Installation
```bash
composer require visifo/smack-clause
```

## Usage

### The Hard Hit (Mandatory)
`Smack::that()` expects a value. If it's null or fails a rule, it smacks back.

```php
Smack::that($email)->isString()->isEmail();
Smack::that($age)->isInt()->min(18);
```

### The Quiet Pass (Optional)
`Smack::maybe()` allows nulls. If the value is there, it better be right.

```php
Smack::maybe($bio)->isString()->max(200);
```

### Hitting Collections

```php
Smack::that($userIDs)->each()->isInt()->isPositive();
```

## Custom Smacks
Extend the library with your own logic while keeping `Smack::that(...)->...` syntax.

```php
use App\Smack\PlayerSmack;
use Visifo\SmackClause\Smack;

Smack::register(PlayerSmack::class);

Smack::that($player)
    ->isPlayer()
    ->isNotUn()
    ->isInPlayState();
```

You can register as many project-specific root methods as you need, one class at a time:

```php
Smack::register(PlayerSmack::class);
Smack::register(VatSmack::class);
```

`CustomSmack` is the base class for domain smacks. Add `#[SmackMethod('...')]` on the class and implement `fromSmack(...)`:

```php
use Visifo\SmackClause\CustomSmack;
use Visifo\SmackClause\SmackException;
use Visifo\SmackClause\SmackMethod;
use Visifo\SmackClause\Trace;

#[SmackMethod('isPlayer')]
final readonly class PlayerSmack extends CustomSmack
{
    public function __construct(
        private GamePlayer $player,
        Trace $trace,
    ) {
        parent::__construct($trace);
    }

    public static function fromSmack(
        mixed $value,
        Trace $trace,
        mixed ...$arguments,
    ): static {
        if (! $value instanceof GamePlayer) {
            throw SmackException::forExpectedType(GamePlayer::class, $value, $trace);
        }

        return new self($value, $trace);
    }

    public function isNotUn(): self
    {
        $this->ensure(! $this->player->isUn(), 'not UN', $this->player);

        return $this;
    }
}
```

See [tests/Fixtures/Smacks/PlayerSmack.php](tests/Fixtures/Smacks/PlayerSmack.php) for a complete example.

## IDE Helper
For dynamic custom methods (`isPlayer()`, `isVat()`, ...), generate a typed helper class for IDE/static tooling:

```bash
vendor/bin/smack-ide-helper
```

Available parameters:

- `--root=<path>`
  - Default: current working directory (`getcwd()`).
  - Purpose: project root used for `composer.json`, autoload, and output file location.
- `--scan=<path>`
  - Default: all `autoload.psr-4` directories from `<root>/composer.json`.
  - Purpose: limit scanning to specific directory.
  - Notes: can be passed multiple times; when provided, only these directories are scanned.

This command generates `_smack_ide_helper.php` in your project root with a `Visifo\\SmackClause\\IdeHelperSmack` class that contains `@method` annotations for dynamic smack methods. `Smack` is linked to it via `@mixin`.

After generating, static tooling can understand calls like:

```php
use Visifo\SmackClause\Smack;

Smack::that($player)
    ->isPlayer()
    ->isNotUn();
```

The generator is strict and fails if any `CustomSmack` implementation is invalid (missing `#[SmackMethod('...')]`, duplicate method name, invalid class, etc.).

## The Violation Report
When a check fails, `SmackViolation` *(extends `InvalidArgumentException`)* gives you the forensics:
- **Path**: The variable or nested key name.
- **Value**: What was actually received.
- **Rule**: Which assertion failed.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

### Branding & Tone Guide
[TONE.md](docs/TONE.md) file ensures that anyone contributing to the project maintains the "Smack" vibe.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sergej Tihonov](https://github.com/Sergej-Tihonov)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
