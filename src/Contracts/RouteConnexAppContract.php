<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Contracts;

/**
 * A RouteConnex App: one pair of Credentials, with its Callback token and an
 * optional base URL. Config Apps and App models both implement it, so the
 * rest of the package only works with the contract.
 */
interface RouteConnexAppContract
{
    /**
     * The App's key, used to find it again: a config App's name, or a model's
     * key (an integer, a UUID, a MongoDB ObjectId). `null` while unsaved.
     */
    public function getRouteConnexKey(): string|int|null;

    /**
     * The client ID. Empty or `null` makes calls throw a configuration error.
     */
    public function getRouteConnexClientId(): ?string;

    /**
     * The client secret. Empty or `null` makes calls throw a configuration error.
     */
    public function getRouteConnexClientSecret(): ?string;

    /**
     * The secret RouteConnex sends back in every Callback, or `null` when the
     * App has none.
     */
    public function getRouteConnexCallbackToken(): ?string;

    /**
     * The App's own API base URL, or `null` to use the global `base_url`.
     */
    public function getRouteConnexBaseUrl(): ?string;
}
