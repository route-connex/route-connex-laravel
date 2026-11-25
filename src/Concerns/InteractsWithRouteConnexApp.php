<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Concerns;

use RouteConnex\Laravel\Contracts\RouteConnexAppContract;

/**
 * Implements `RouteConnexAppContract` on an Eloquent model from its
 * `client_id`, `client_secret`, `callback_token` and `base_url` attributes.
 * Override a method to read another column. The trait neither generates a
 * Callback token nor encrypts anything: cast the columns as needed.
 *
 * @phpstan-require-implements RouteConnexAppContract
 */
trait InteractsWithRouteConnexApp
{
    public function getRouteConnexKey(): string|int|null
    {
        $key = $this->getKey();

        return is_string($key) || is_int($key) ? $key : null;
    }

    public function getRouteConnexClientId(): ?string
    {
        return $this->routeConnexString('client_id');
    }

    public function getRouteConnexClientSecret(): ?string
    {
        return $this->routeConnexString('client_secret');
    }

    public function getRouteConnexCallbackToken(): ?string
    {
        return $this->routeConnexString('callback_token');
    }

    public function getRouteConnexBaseUrl(): ?string
    {
        return $this->routeConnexString('base_url');
    }

    protected function routeConnexString(string $attribute): ?string
    {
        $value = $this->getAttribute($attribute);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
