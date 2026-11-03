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

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- One of these databases:
  - MySQL 8.0+
  - PostgreSQL 14+
  - MongoDB 7.0+ (requires [`mongodb/laravel-mongodb`](https://github.com/mongodb/laravel-mongodb) ^5.12 and the `mongodb` PHP extension)
  - SQLite

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

MySQL, PostgreSQL and MongoDB run as Compose services (the `databases` profile). `make test-mysql`, `make test-pgsql` and `make test-mongodb` start them as needed, and `make db-down` stops and removes them.

By default they run the latest tested versions (MySQL 8.4, PostgreSQL 18, MongoDB 8). Set `MYSQL_VERSION`, `POSTGRES_VERSION` or `MONGO_VERSION` to test another version, e.g. the minimum supported ones:

```bash
MYSQL_VERSION=8.0 make test-mysql
POSTGRES_VERSION=14 make test-pgsql
MONGO_VERSION=7.0 make test-mongodb
```

CI tests MySQL 8.0 and 8.4, PostgreSQL 14, 17 and 18, and MongoDB 7.0 and 8, each on Laravel 12 and 13.

## Contributing

Thank you for considering contributing to RouteConnex! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Credits

- [Leo Oberle](https://github.com/route-connex)
- [All Contributors](../../contributors)

## License

RouteConnex is open-sourced software licensed under the [MIT license](LICENSE).
