<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use GuzzleHttp\Psr7\Response;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Endpoints\MicrosoftSharepoint\RouteConnexFilesEndpoint;
use RouteConnex\Laravel\Http\RouteConnexResponse;

/**
 * A `files` endpoint that records what `result()` receives and returns a
 * marker instead of the response.
 */
final class RecordingFilesEndpoint extends RouteConnexFilesEndpoint
{
    /**
     * @var list<array<string, mixed>>
     */
    public array $received = [];

    public static function marker(): RouteConnexResponse
    {
        return new RouteConnexResponse(new Response(200, [], 'marker'));
    }

    protected function result(RouteConnexAppContract $app, string $service, array $request, array $meta, RouteConnexResponse $response): RouteConnexResponse
    {
        $this->received[] = compact('app', 'service', 'request', 'meta', 'response');

        return self::marker();
    }
}
