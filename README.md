<div class="filament-hidden">

![Filament SSO Server](https://raw.githubusercontent.com/jeffersongoncalves/filament-sso-server/1.x/art/jeffersongoncalves-filament-sso-server.png)

</div>

# Filament SSO Server

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/filament-sso-server.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/filament-sso-server)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/filament-sso-server/tests.yml?branch=1.x&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/filament-sso-server/actions?query=workflow%3Atests+branch%3A1.x)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/filament-sso-server/pint.yml?branch=1.x&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/filament-sso-server/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3A1.x)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/filament-sso-server.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/filament-sso-server)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/filament-sso-server.svg?style=flat-square)](LICENSE.md)

Filament admin UI for [jeffersongoncalves/laravel-sso-server](https://github.com/jeffersongoncalves/laravel-sso-server): register SSO client apps, rotate their secrets, and watch or revoke the sessions they hold, all from your panel.

## Features

- **SSO clients**: create, edit, activate/deactivate and delete client apps. `client_id` (UUID) and a 64-char `client_secret` are generated server-side and the secret is shown once in a notification.
- **Secret rotation**: one click, from the table or the edit page. The old secret stops working immediately.
- **Active sessions**: list every issued access token with its client, user (`sub`) and expiry, filtered by client or validity.
- **Revoke**: delete one session (the token stops validating on `/userinfo`) or **log a user out everywhere**, which revokes all their sessions and fires the Single Logout webhooks.
- **Overview widget**: active clients, live sessions and distinct connected users, refreshed every 30s.
- Translations: English and Brazilian Portuguese.

## Compatibility

| Package Version | Filament Version |
|-----------------|------------------|
| [1.x](https://github.com/jeffersongoncalves/filament-sso-server/tree/1.x) | 3.x |
| [2.x](https://github.com/jeffersongoncalves/filament-sso-server/tree/2.x) | 4.x |
| [3.x](https://github.com/jeffersongoncalves/filament-sso-server/tree/3.x) | 5.x |

## Installation

Install and set up [laravel-sso-server](https://github.com/jeffersongoncalves/laravel-sso-server) first (migrations and signing keys), then install the plugin via composer:

```bash
composer require jeffersongoncalves/filament-sso-server:"^1.0"
```

## Usage

Register the plugin in your panel provider:

```php
use JeffersonGoncalves\Filament\SsoServer\SsoServerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            SsoServerPlugin::make(),
        ]);
}
```

### Options

```php
SsoServerPlugin::make()
    ->navigationGroup('Authentication') // default: "SSO Server" (translated)
    ->clientResource(false)             // hide the SSO clients resource
    ->sessionResource(false)            // hide the sessions resource
    ->overviewWidget(false);            // don't add the stats widget to the dashboard
```

### Translations

Publish the translations to customize labels:

```bash
php artisan vendor:publish --tag="filament-sso-server-translations"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
