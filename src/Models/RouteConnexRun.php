<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Enums\RouteConnexRunStatus;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\Laravel\Exceptions\RouteConnexRequestException;
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
     * Get the Run from RouteConnex with the Run's own App, then update it from
     * the answer (see `updateFromRouteConnex()`). Returns the Run.
     *
     * @throws RouteConnexConfigurationException When the Run's App can't be found
     * @throws RouteConnexRequestException When the answer has no known status
     */
    public function refreshFromRouteConnex(): static
    {
        $response = app(RouteConnex::class)->app($this->app)->run()->_id_($this->run_id)->get();

        $data = $response->json('data');
        $data = is_array($data) ? $data : [];

        $status = $data['status'] ?? null;
        $status = is_array($status) ? ($status['id'] ?? null) : $status;

        $this->updateFromRouteConnex(
            (is_string($status) ? RouteConnexRunStatus::tryFrom($status) : null)
                ?? throw RouteConnexRequestException::unknownRunStatus($response, $status),
            $data,
        );

        return $this;
    }

    /**
     * Update the Run with the state RouteConnex reported, from the Run's
     * `data` as the API (or a Callback) sends it. Returns whether the status
     * changed.
     *
     * A finished status is final, as it is in RouteConnex: once the Run has
     * one, nothing is updated. A different finished status is ignored with a
     * warning in the log; a late unfinished one is ignored silently.
     * Otherwise the status, the type and the response are saved, and
     * `finished_at` is set when a finished status first arrives (the API
     * sends no finish time).
     *
     * @param  array<array-key, mixed>  $data
     */
    public function updateFromRouteConnex(RouteConnexRunStatus $status, array $data): bool
    {
        if ($this->status->isFinished()) {
            if ($status->isFinished() && $status !== $this->status) {
                Log::warning("RouteConnex Run [{$this->run_id}] is already {$this->status->value}; ignored the status {$status->value}.");
            }

            return false;
        }

        $changed = $status !== $this->status;

        $type = is_array($data['type'] ?? null) ? ($data['type']['name'] ?? null) : null;

        $this->status = $status;

        if (is_string($type) && $type !== '') {
            $this->type = $type;
        }

        if (config('route-connex-laravel.runs.store_response', true)) {
            $this->response = $data;
        }

        if ($status->isFinished()) {
            $this->finished_at = now();
        }

        $this->save();

        return $changed;
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
