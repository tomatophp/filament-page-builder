![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-page-builder/master/art/screenshot.jpg)

# Filament page builder

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-page-builder/version.svg)](https://packagist.org/packages/tomatophp/filament-page-builder)
[![License](https://poser.pugx.org/tomatophp/filament-page-builder/license.svg)](https://packagist.org/packages/tomatophp/filament-page-builder)
[![Downloads](https://poser.pugx.org/tomatophp/filament-page-builder/d/total.svg)](https://packagist.org/packages/tomatophp/filament-page-builder)

Manage your pages urls and build a page using dynamic Section content with drop and drag for FilamentPHP

## Installation

```bash
composer require tomatophp/filament-page-builder
```
after install your package please run this command

```bash
php artisan filament-page-builder:install
```

finally register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\FilamentPageBuilder\FilamentPageBuilderPlugin::make())
```


## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-page-builder-config"
```

you can publish views file by use this command

```bash
php artisan vendor:publish --tag="filament-page-builder-views"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-page-builder-lang"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="filament-page-builder-migrations"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
