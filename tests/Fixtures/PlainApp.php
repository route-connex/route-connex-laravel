<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use RouteConnex\Laravel\Contracts\RouteConnexAppContract;

/**
 * An App that isn't an Eloquent model, e.g. built from a settings store.
 */
final readonly class PlainApp implements RouteConnexAppContract
{
    public function __construct(
        private ?string $clientId,
        private ?string $clientSecret,
        private string|int|null $key = null,
        private ?string $baseUrl = null,
    ) {
        //
    }

    public function getRouteConnexKey(): string|int|null
    {
        return $this->key;
    }

    public function getRouteConnexClientId(): ?string
    {
        return $this->clientId;
    }

    public function getRouteConnexClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function getRouteConnexCallbackToken(): ?string
    {
        return null;
    }

    public function getRouteConnexBaseUrl(): ?string
    {
        return $this->baseUrl;
    }
}
