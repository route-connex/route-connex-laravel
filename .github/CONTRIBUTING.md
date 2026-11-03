# Contribution Guide

Thank you for considering contributing to RouteConnex! Please review the following guidelines before submitting a pull request.

For significant changes, please open an issue first so we can discuss the approach.

## Process

1. Fork the project
2. Create a new branch
3. Code, test, commit, and push
4. Open a pull request detailing your changes

## Guidelines

- Ensure the coding style passes by running `make lint`.
- Send a coherent commit history, making sure each commit in your pull request is meaningful.
- You may need to [rebase](https://git-scm.com/book/en/v2/Git-Branching-Rebasing) to avoid merge conflicts.
- Please remember that we follow [SemVer](http://semver.org/).

## Setup

Clone your fork, then build the PHP image and install the dev dependencies. No local PHP is needed, because everything runs in Docker:

```bash
make build
make install
```

If you have PHP 8.3+ and Composer locally, `composer install` and the `composer` scripts work too.

## Lint

Fix the code style with Pint:

```bash
make lint
```

To check the style without changing files, run `make composer ARGS="lint:check"`.

## Tests

Run the full suite (PHPStan, Pint, type coverage and Pest, on SQLite):

```bash
make test
```

To run only Pest, or a subset of it, pass `ARGS` through to Pest:

```bash
make test-unit ARGS="--filter=DatabaseTest"
```

Changes that touch database code also need the other databases. `make test-all` runs Pest against SQLite, MySQL, PostgreSQL and MongoDB. `make test-mysql`, `make test-pgsql` and `make test-mongodb` run one database each and accept `ARGS` too. To check a minimum supported version, set its version variable:

```bash
MYSQL_VERSION=8.0 make test-mysql
POSTGRES_VERSION=14 make test-pgsql
MONGO_VERSION=7.0 make test-mongodb
```

