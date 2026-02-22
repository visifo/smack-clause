# SmackClause is a punchy, no-nonsense guard layer: it hits bad input fast, exits early, and leaves a clean trace of what failed, where, and why. The vibe is direct and a little aggressive, but the behavior is precise: stack small, composable rules, get strong default messages, and keep the happy path readable.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/visifo/smackclause.svg?style=flat-square)](https://packagist.org/packages/visifo/smackclause)
[![Tests](https://img.shields.io/github/actions/workflow/status/visifo/smackclause/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/visifo/smackclause/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/visifo/smackclause.svg?style=flat-square)](https://packagist.org/packages/visifo/smackclause)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/SmackClause.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/SmackClause)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require visifo/smackclause
```

## Usage

```php
$skeleton = new Visifo\Smack();
echo $skeleton->echoPhrase('Hello, Visifo!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sergej Tihonov](https://github.com/Sergej-Tihonov)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
