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
use App\Smack\Provider\GameSmackProvider;
use Visifo\SmackClause\Smack;

Smack::registerProvider(new GameSmackProvider());

Smack::that($player)
    ->isPlayer()
    ->isNotUn()
    ->isInPlayState();
```

You can register one-off methods directly:

```php
Smack::register('isVat', function (mixed $value, Trace $trace): VatSmack {
    if (! is_string($value)) {
        throw SmackException::forExpectedType('string', $value, $trace);
    }

    return new VatSmack($value, $trace);
});
```

Provider contract:

```php
use Visifo\SmackClause\SmackProviderInterface;
use Visifo\SmackClause\SmackRegistry;

final class GameSmackProvider implements SmackProviderInterface
{
    public function register(SmackRegistry $registry): void
    {
        $registry->register('isPlayer', function (mixed $value, Trace $trace): PlayerSmack {
            if (! $value instanceof GamePlayer) {
                throw SmackException::forExpectedType(GamePlayer::class, $value, $trace);
            }

            return new PlayerSmack($value, $trace);
        });
    }
}
```

`CustomSmack` can be used as a base class for domain smacks:

```php
use Visifo\SmackClause\CustomSmack;

final readonly class PlayerSmack extends CustomSmack
{
    public function __construct(
        private GamePlayer $player,
        Trace $trace,
    ) {
        parent::__construct($trace);
    }

    public function isNotUn(): self
    {
        $this->ensure(! $this->player->isUn(), 'not UN', $this->player);

        return $this;
    }
}
```

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
