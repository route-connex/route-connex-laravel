<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Endpoints\MicrosoftSharepoint\RouteConnexFilesEndpoint;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\Laravel\Models\RouteConnexRun;

/**
 * A `files` endpoint that records what `result()` receives and returns an
 * unsaved marker Run instead of storing one.
 */
final class RecordingFilesEndpoint extends RouteConnexFilesEndpoint
{
    /**
     * @var list<array<string, mixed>>
     */
    public array $received = [];

    public static function marker(): RouteConnexRun
    {
        return new RouteConnexRun(['run_id' => 'marker']);
    }

    protected function result(RouteConnexAppContract $app, string $service, array $request, array $meta, RouteConnexResponse $response): RouteConnexRun
    {
        $this->received[] = compact('app', 'service', 'request', 'meta', 'response');

        return self::marker();
    }
}
