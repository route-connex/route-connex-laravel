<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Enums\RouteConnexRunStatus;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\Laravel\RouteConnex;

/**
 * A Run the package started, stored in the consumer app's database. It
 * belongs to a config App (`app_name`) or a model App (`app_id`, found
 * through `models.app`), never both. Extend it (for MongoDB's
 * `DocumentModel`, another table or connection, extra fields) and set the
 * subclass in `models.run`.
 *
 * @property string $run_id
 * @property string|null $app_name
 * @property string|null $app_id
 * @property string $service
 * @property string|null $type
 * @property RouteConnexRunStatus $status
 * @property array<array-key, mixed>|null $meta
 * @property array<array-key, mixed>|null $request
 * @property array<array-key, mixed>|null $response
 * @property Carbon|null $finished_at
 * @property Carbon|null $purged_at
 * @property-read RouteConnexAppContract $app
 */
class RouteConnexRun extends Model
{
    protected $table = 'route_connex_runs';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'run_id',
        'app_name',
        'app_id',
        'service',
        'type',
        'status',
        'meta',
        'request',
        'response',
        'finished_at',
    ];

    protected static function booted(): void
    {
        static::saving(function (RouteConnexRun $run): void {
            if (filled($run->app_name) === filled($run->app_id)) {
                throw RouteConnexConfigurationException::invalidRunApp();
            }
        });
    }

    /**
     * The App that started the Run: the config App named by `app_name`, or
     * the model App with the key `app_id`, loaded through `models.app` from
     * that model's own connection.
     *
     * @return Attribute<RouteConnexAppContract, never>
     *
     * @throws RouteConnexConfigurationException When the App can't be found
     */
    protected function app(): Attribute
    {
        return Attribute::get(function (): RouteConnexAppContract {
            if (filled($this->app_name)) {
                return app(RouteConnex::class)->app((string) $this->app_name)->getApp();
            }

            /** @var class-string<Model&RouteConnexAppContract> $model */
            $model = config('route-connex-laravel.models.app');
            $id = (string) $this->app_id;

            $app = $model::query()->find($id);

            if (! $app instanceof RouteConnexAppContract) {
                throw RouteConnexConfigurationException::missingRunApp($model, $id);
            }

            return $app;
        })->shouldCache();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RouteConnexRunStatus::class,
            'meta' => 'array',
            'request' => 'array',
            'response' => 'array',
            'finished_at' => 'datetime',
            'purged_at' => 'datetime',
        ];
    }
}
