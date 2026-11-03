<div align="center">
    <h1>RouteConnex</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/route-connex/route-connex-laravel"><img src="https://img.shields.io/packagist/v/route-connex/route-connex-laravel.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/route-connex/route-connex-laravel"><img src="https://img.shields.io/packagist/php-v/route-connex/route-connex-laravel.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/route-connex/route-connex-laravel"><img src="https://badge.laravel.cloud/badge/route-connex/route-connex-laravel?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/route-connex/route-connex-laravel/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/route-connex/route-connex-laravel/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/route-connex/route-connex-laravel"><img src="https://img.shields.io/packagist/dt/route-connex/route-connex-laravel.svg?style=flat-square" alt="Total Downloads"></a>
</p>

Laravel plugin for route-connex

## Installation

You can install the package via Composer:

```bash
composer require route-connex/route-connex-laravel
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="route-connex-laravel"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="route-connex-laravel-config"
```

### Publishing and Running the Migrations

```bash
php artisan vendor:publish --tag="route-connex-laravel-migrations"
php artisan migrate
```

### Publishing the Views

```bash
php artisan vendor:publish --tag="route-connex-laravel-views"
```

### Publishing the Translations

```bash
php artisan vendor:publish --tag="route-connex-laravel-lang"
```

### Publishing the Public Assets

```bash
php artisan vendor:publish --tag="route-connex-laravel-assets"
```

## Usage

<!-- Add a basic usage example here. -->

## Development

No local PHP is needed: everything runs in Docker through `make` (run `make help` for the full list).

```bash
make build      # build the PHP 8.4 image
make install    # composer install
make test       # PHPStan, Pint, type coverage and Pest (SQLite)
make test-all   # Pest against SQLite, MySQL, PostgreSQL and MongoDB
make serve      # workbench app on http://localhost:8000
make testbench ARGS="route:list"   # artisan for the package
make shell      # shell inside the container
```

MySQL 8.4, PostgreSQL 17 and MongoDB 8 run as Compose services (the `databases` profile). `make test-mysql`, `make test-pgsql` and `make test-mongodb` start them as needed, and `make db-down` stops them.

## Contributing

Thank you for considering contributing to RouteConnex! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Credits

- [Leo Oberle](https://github.com/route-connex)
- [All Contributors](../../contributors)

## License

RouteConnex is open-sourced software licensed under the [MIT license](LICENSE).
