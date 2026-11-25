<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use RouteConnex\Laravel\Concerns\InteractsWithRouteConnexApp;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\RouteConnex;

/**
 * An App stored in the database, for example one per Tenant. The client
 * secret and the Callback token are encrypted and hidden, and a Callback
 * token is generated when an App is created without one. Extend it (for
 * MongoDB's `DocumentModel`, another table or connection) and set the
 * subclass in `models.app`.
 *
 * @property string $name
 * @property string $client_id
 * @property string $client_secret
 * @property string $callback_token
 * @property string|null $base_url
 */
class RouteConnexApp extends Model implements RouteConnexAppContract
{
    use InteractsWithRouteConnexApp;

    protected $table = 'route_connex_apps';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'client_id',
        'client_secret',
        'callback_token',
        'base_url',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'client_secret',
        'callback_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (RouteConnexApp $app): void {
            if ($app->getRouteConnexCallbackToken() === null) {
                $app->callback_token = app(RouteConnex::class)->generateCallbackToken();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'client_secret' => 'encrypted',
            'callback_token' => 'encrypted',
        ];
    }
}
