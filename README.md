![Screenshot](https://raw.githubusercontent.com/tomatophp/filament-workflows/master/art/screenshot.jpg)

# Filament Workflows Builder

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-workflows/version.svg)](https://packagist.org/packages/tomatophp/filament-workflows)
[![License](https://poser.pugx.org/tomatophp/filament-workflows/license.svg)](https://packagist.org/packages/tomatophp/filament-workflows)
[![Downloads](https://poser.pugx.org/tomatophp/filament-workflows/d/total.svg)](https://packagist.org/packages/tomatophp/filament-workflows)

Manage and generate workflows using Filament Account Builder and Form Builder with multi approve steps

## Installation

```bash
composer require tomatophp/filament-workflows
```
after install your package please run this command

```bash
php artisan filament-workflows:install
```

finally register the plugin on `/app/Providers/Filament/AdminPanelProvider.php`

```php
->plugin(\TomatoPHP\FilamentWorkflows\FilamentWorkflowsPlugin::make())
```


## Publish Assets

you can publish config file by use this command

```bash
php artisan vendor:publish --tag="filament-workflows-config"
```

you can publish views file by use this command

```bash
php artisan vendor:publish --tag="filament-workflows-views"
```

you can publish languages file by use this command

```bash
php artisan vendor:publish --tag="filament-workflows-lang"
```

you can publish migrations file by use this command

```bash
php artisan vendor:publish --tag="filament-workflows-migrations"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
