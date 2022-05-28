<h1 align="center">{{name}}</h1>

<p align="center">
    <strong>{{description}}</strong>
</p>

<p align="center">
    <a href="https://github.com/{{vendor}}/{{package}}/blob/{{branch}}/README.md"><img alt="License" src="https://img.shields.io/github/license/{{vendor}}/{{package}}?style=flat-square"></a>
    <a href="https://php.net"><img alt="PHP Version Support" src="https://img.shields.io/packagist/php-v/{{vendor}}/{{package}}?style=flat-square"></a>
    <a href="https://packagist.org/packages/{{vendor}}/{{package}}"><img alt="Packagist Version" src="https://img.shields.io/packagist/v/{{vendor}}/{{package}}?style=flat-square"></a>
    <a href="https://packagist.org/packages/{{vendor}}/{{package}}"><img alt="Packagist Downloads" src="https://img.shields.io/packagist/dt/{{vendor}}/{{package}}?style=flat-square"></a>
    <a href="https://github.com/{{vendor}}/{{package}}/actions/workflows/fix-code-style.yml"><img alt="Code style check & fix status" src="https://img.shields.io/github/workflow/status/{{vendor}}/{{package}}/Check%20&%20fix%20styling?label=code%20style&style=flat-square"></a>
    <a href="https://github.com/{{vendor}}/{{package}}/actions/workflows/analyse.yml"><img alt="Code Static Analysis Status" src="https://img.shields.io/github/workflow/status/{{vendor}}/{{package}}/Code%20Static%20Analysis?label=analyse&style=flat-square"></a>
    <a href="https://github.com/{{vendor}}/{{package}}/actions/workflows/run-tests.yml"><img alt="GitHub Workflow Status" src="https://img.shields.io/github/workflow/status/{{vendor}}/{{package}}/Tests?label=tests&style=flat-square"></a>
</p>

## About

Let people know what your project can do specifically. Provide context and add a link to any reference visitors might be
unfamiliar with. A list of <strong>Features</strong> or a <strong>Background</strong> subsection can also be added here.
If there are alternatives to your
project, this is a good place to list differentiating factors.

## Installation

You can install the package via composer:

```bash
composer require {{vendor}}/{{package}}
```

## Usage

```php
use Awuxtron\PackageSkeleton\Example;

echo (new Example)->text('Hello world!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Roadmap

- [x] Init Project
- [ ] Edit README.md
- [ ] Write Your Code
    - [ ] Nested Feature

See the [open issues](../../issues) for a full list of proposed features (and
known issues).

## Contributing

Please see [CONTRIBUTING](../../.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [{{author}}](https://github.com/{{vendor}})
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
