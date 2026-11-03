<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests;

use MongoDB\Laravel\MongoDBServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RouteConnex\Laravel\RouteConnexServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        $providers = [
            RouteConnexServiceProvider::class,
        ];

        if (static::databaseConnection() === 'mongodb') {
            $providers[] = MongoDBServiceProvider::class;
        }

        return $providers;
    }

    /**
     * Point the default connection at the database selected by DB_CONNECTION
     * (sqlite, mysql, pgsql or mongodb). Defaults match docker-compose.yml.
     */
    protected function defineEnvironment($app): void
    {
        $connection = static::databaseConnection();
        $host = getenv('DB_HOST') ?: $connection;
        $database = getenv('DB_DATABASE') ?: 'testing';
        $username = getenv('DB_USERNAME') ?: 'testing';
        $password = getenv('DB_PASSWORD') ?: 'testing';

        $app['config']->set('database.default', $connection);
        $app['config']->set("database.connections.{$connection}", match ($connection) {
            'sqlite' => array_merge($this->laravelConnection($app, $connection), [
                'database' => ':memory:',
                'foreign_key_constraints' => true,
            ]),
            'mysql' => array_merge($this->laravelConnection($app, $connection), [
                'host' => $host,
                'port' => getenv('DB_PORT') ?: 3306,
                'database' => $database,
                'username' => $username,
                'password' => $password,
            ]),
            'pgsql' => array_merge($this->laravelConnection($app, $connection), [
                'host' => $host,
                'port' => getenv('DB_PORT') ?: 5432,
                'database' => $database,
                'username' => $username,
                'password' => $password,
            ]),
            'mongodb' => [
                'driver' => 'mongodb',
                'dsn' => sprintf('mongodb://%s:%s', $host, getenv('DB_PORT') ?: 27017),
                'database' => $database,
            ],
        });
    }

    /**
     * Laravel's own config for a connection, so the tests run on the same
     * settings (strict, prefix_indexes, sslmode, ...) as a Laravel app.
     *
     * @return array<string, mixed>
     */
    protected function laravelConnection($app, string $connection): array
    {
        return $app['config']->get("database.connections.{$connection}");
    }

    protected static function databaseConnection(): string
    {
        return getenv('DB_CONNECTION') ?: 'sqlite';
    }
}
