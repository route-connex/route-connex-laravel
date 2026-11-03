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
            'sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'mysql' => [
                'driver' => 'mysql',
                'host' => $host,
                'port' => getenv('DB_PORT') ?: 3306,
                'database' => $database,
                'username' => $username,
                'password' => $password,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ],
            'pgsql' => [
                'driver' => 'pgsql',
                'host' => $host,
                'port' => getenv('DB_PORT') ?: 5432,
                'database' => $database,
                'username' => $username,
                'password' => $password,
                'charset' => 'utf8',
                'prefix' => '',
                'search_path' => 'public',
            ],
            'mongodb' => [
                'driver' => 'mongodb',
                'dsn' => sprintf('mongodb://%s:%s', $host, getenv('DB_PORT') ?: 27017),
                'database' => $database,
            ],
        });
    }

    protected static function databaseConnection(): string
    {
        return getenv('DB_CONNECTION') ?: 'sqlite';
    }
}
