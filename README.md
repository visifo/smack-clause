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
Extend the library with your own logic:

```php
Smack::register('isVat', fn($val) => str_starts_with($val, 'DE'));

Smack::that($vat)->isVat();
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
