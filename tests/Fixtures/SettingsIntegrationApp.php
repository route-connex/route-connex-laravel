<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

/**
 * A consumer app's own model whose client ID lives in another column: it
 * overrides one of the trait's methods.
 */
final class SettingsIntegrationApp extends IntegrationApp
{
    protected $casts = ['settings' => 'array'];

    public function getRouteConnexClientId(): string
    {
        $settings = $this->getAttribute('settings');

        return is_array($settings) && is_string($settings['route_connex_client_id'] ?? null)
            ? $settings['route_connex_client_id']
            : '';
    }
}
