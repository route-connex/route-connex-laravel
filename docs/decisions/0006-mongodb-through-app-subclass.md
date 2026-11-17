# 0006: MongoDB through the consumer app's model subclass

**Status:** accepted **Date:** 2026-11-17

## Context

The package must work on MySQL, PostgreSQL, SQLite and MongoDB. On MongoDB, Eloquent models need laravel-mongodb's `DocumentModel` trait. Most consumer apps don't use MongoDB, and the package shouldn't force them to install it. A consumer app may also split its data, for example Apps in MySQL and Runs in MongoDB.

## Decision

The shipped models extend Laravel's `Model`. An app on MongoDB extends them with the `DocumentModel` trait (and its connection) and sets the subclass in `models.app` or `models.run`. `mongodb/laravel-mongodb` stays a Composer `suggest` for consumer apps, and the package's own code never uses the trait. It is a `require-dev` dependency, so the package's tests run on MongoDB (`make test-mongodb`) next to the SQL databases. A Run on one database can belong to an App on another.

## Consequences

- Apps that don't use MongoDB don't install it.
- Every MongoDB consumer writes a small subclass; the docs show it.
- Migrations and tests must be portable: the migration only creates the collection on MongoDB, and column types are chosen to work everywhere (string keys, string status, JSON columns).
- The package's own tests can only cover the subclass pattern, not every consumer's setup.
