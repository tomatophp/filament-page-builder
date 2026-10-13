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


## Builder Routes and Authorization

The builder registers these routes (names in brackets):

- `GET admin/pages/{model}/builder` (`admin.pages.builder`)
- `GET admin/pages/{model}/meta` (`admin.pages.meta`)
- `POST admin/pages/{model}/meta` (`admin.pages.meta.store`)
- `POST admin/pages/{model}/sections` (`admin.pages.sections`)
- `POST admin/pages/{model}/sections/remove` (`admin.pages.remove`)
- `POST admin/pages/{model}/clear` (`admin.pages.clear`)

Every request is authorized with your application's policy for the page model, using the `update` ability. **You must register a policy for the page model** (Filament Shield generates one). Without a policy, every builder request returns `403`.

```php
use Illuminate\Support\Facades\Gate;
use TomatoPHP\FilamentCms\Models\Post;

Gate::policy(Post::class, \App\Policies\PostPolicy::class);
```

Pages with a `team_id` can only be edited by users who can access that team on a Filament panel with tenancy.

Publish the config to change the page model, the middleware, or to turn the routes off:

```php
// config/filament-page-builder.php
'routes' => [
    'enabled' => env('FILAMENT_PAGE_BUILDER_ROUTES', true),
    'middleware' => ['web', 'auth'],
],
'model' => \TomatoPHP\FilamentCms\Models\Post::class,
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
