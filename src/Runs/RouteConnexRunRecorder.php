<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Runs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Enums\RouteConnexRunStatus;
use RouteConnex\Laravel\Events\RouteConnexRunCreated;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\Laravel\Exceptions\RouteConnexRequestException;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\Laravel\Models\RouteConnexRun;

/**
 * Stores the `RouteConnexRun` of a System call once the API has answered with
 * a Run ID. It is resolved from the container on every call: bind a subclass
 * to change one step, for example to fill extra columns.
 */
class RouteConnexRunRecorder
{
    /**
     * Make sure a Run of this App can be stored, before the request is sent:
     * a config App always can, any other App needs a key.
     *
     * @throws RouteConnexConfigurationException
     */
    public function checkApp(RouteConnexAppContract $app): void
    {
        if ($app instanceof RouteConnexConfigApp) {
            return;
        }

        $key = $app->getRouteConnexKey();

        if ($key === null || $key === '') {
            throw RouteConnexConfigurationException::appWithoutKey($app);
        }
    }

    /**
     * Store the Run the API just started and fire `RouteConnexRunCreated`.
     *
     * @param  string  $service  The Service and HTTP method, e.g. `POST microsoft-sharepoint/files`.
     * @param  array{query: array<string, mixed>, body: array<string, mixed>}  $request  As sent, `_meta_` included.
     * @param  array<string, mixed>  $meta
     *
     * @throws RouteConnexRequestException When the answer has no Run ID or an unknown status
     * @throws RouteConnexConfigurationException
     */
    public function record(RouteConnexAppContract $app, string $service, array $request, array $meta, RouteConnexResponse $response): RouteConnexRun
    {
        $run = $this->newRun($app);

        $run->fill($this->attributes($app, $service, $request, $meta, $response, $this->data($response)));

        $this->save($run);
        $this->dispatchCreated($run);

        return $run;
    }

    /**
     * The attributes of the new Run.
     *
     * @param  array{query: array<string, mixed>, body: array<string, mixed>}  $request
     * @param  array<string, mixed>  $meta
     * @param  array<array-key, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws RouteConnexRequestException
     */
    protected function attributes(RouteConnexAppContract $app, string $service, array $request, array $meta, RouteConnexResponse $response, array $data): array
    {
        return array_merge($this->appAttributes($app), [
            'run_id' => $this->runId($response, $data),
            'service' => $service,
            'type' => $this->type($data),
            'status' => $this->status($response, $data),
            'meta' => $meta,
            'request' => config('route-connex-laravel.runs.store_request', true) ? $request : null,
            'response' => config('route-connex-laravel.runs.store_response', true) ? $data : null,
        ]);
    }

    /**
     * The answer's `data` object, or an empty array.
     *
     * @return array<array-key, mixed>
     */
    protected function data(RouteConnexResponse $response): array
    {
        $data = $response->json('data');

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<array-key, mixed>  $data
     *
     * @throws RouteConnexRequestException
     */
    protected function runId(RouteConnexResponse $response, array $data): string
    {
        $id = $data['id'] ?? null;

        if ((! is_string($id) && ! is_int($id)) || $id === '') {
            throw RouteConnexRequestException::noRunId($response);
        }

        return (string) $id;
    }

    /**
     * `data.status.id`, or `created` when the answer has no status.
     *
     * @param  array<array-key, mixed>  $data
     *
     * @throws RouteConnexRequestException
     */
    protected function status(RouteConnexResponse $response, array $data): RouteConnexRunStatus
    {
        if (! isset($data['status'])) {
            return RouteConnexRunStatus::Created;
        }

        $status = is_array($data['status']) ? ($data['status']['id'] ?? null) : $data['status'];

        return (is_string($status) ? RouteConnexRunStatus::tryFrom($status) : null)
            ?? throw RouteConnexRequestException::unknownRunStatus($response, $status);
    }

    /**
     * `data.type.name`, or `null`.
     *
     * @param  array<array-key, mixed>  $data
     */
    protected function type(array $data): ?string
    {
        $name = is_array($data['type'] ?? null) ? ($data['type']['name'] ?? null) : null;

        return is_string($name) && $name !== '' ? $name : null;
    }

    /**
     * `app_name` for a config App, `app_id` for any other.
     *
     * @return array{app_name: string, app_id: null}|array{app_name: null, app_id: string}
     */
    protected function appAttributes(RouteConnexAppContract $app): array
    {
        if ($app instanceof RouteConnexConfigApp) {
            return ['app_name' => $app->name, 'app_id' => null];
        }

        return ['app_name' => null, 'app_id' => (string) $app->getRouteConnexKey()];
    }

    /**
     * A new instance of the `models.run` class, on its connection.
     */
    protected function newRun(RouteConnexAppContract $app): RouteConnexRun
    {
        /** @var class-string<RouteConnexRun> $model */
        $model = config('route-connex-laravel.models.run');

        $run = new $model;

        return $run->setConnection($this->connection($app, $run));
    }

    /**
     * The Run model's own connection, else the connection of an App that is
     * an Eloquent model (a Tenant's database, say), else the default one.
     */
    protected function connection(RouteConnexAppContract $app, RouteConnexRun $run): ?string
    {
        return $run->getConnectionName()
            ?? ($app instanceof Model ? $app->getConnectionName() : null);
    }

    protected function save(RouteConnexRun $run): void
    {
        $run->save();
    }

    protected function dispatchCreated(RouteConnexRun $run): void
    {
        Event::dispatch(new RouteConnexRunCreated($run));
    }
}
