<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Apps;

use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;

/**
 * An App from the `apps` list in the package config.
 */
final readonly class RouteConnexConfigApp
{
    public function __construct(
        public string $name,
        public string $clientId,
        public string $clientSecret,
        public ?string $baseUrl = null,
    ) {
        //
    }

    /**
     * Build the App from its config entry. The App's own `base_url` wins over
     * the global one; empty values count as unset.
     *
     * @param  array<array-key, mixed>  $config
     *
     * @throws RouteConnexConfigurationException
     */
    public static function fromConfig(string $name, array $config, ?string $globalBaseUrl = null): self
    {
        $clientId = self::string($config['client_id'] ?? null);
        $clientSecret = self::string($config['client_secret'] ?? null);

        if ($clientId === null || $clientSecret === null) {
            throw RouteConnexConfigurationException::missingCredentials($name);
        }

        return new self(
            name: $name,
            clientId: $clientId,
            clientSecret: $clientSecret,
            baseUrl: self::string($config['base_url'] ?? null) ?? self::string($globalBaseUrl),
        );
    }

    private static function string(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
