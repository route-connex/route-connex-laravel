<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints;

use InvalidArgumentException;
use JsonException;
use RouteConnex\Laravel\Contracts\RouteConnexAppContract;
use RouteConnex\Laravel\Exceptions\RouteConnexConfigurationException;
use RouteConnex\Laravel\Exceptions\RouteConnexRequestException;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\Laravel\Models\RouteConnexRun;
use RouteConnex\Laravel\Runs\RouteConnexRunRecorder;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

/**
 * An endpoint of a System (Microsoft SharePoint, HPE Content Manager). It
 * creates a Run, so it takes Meta, and `result()` stores and returns the Run.
 */
abstract class RouteConnexSystemEndpoint extends RouteConnexEndpoint
{
    /**
     * Check that the App's Run can be stored, put the Meta where the API
     * expects it (`_meta_` as JSON in the query for `GET`, as an array in the
     * body otherwise), send the request and return the stored Run.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     * @param  array<string, mixed>  $meta
     *
     * @throws InvalidArgumentException When the Meta can't be encoded as JSON
     * @throws RouteConnexConfigurationException When the App has no key
     * @throws RouteConnexRequestException When the answer has no Run ID or an unknown status
     */
    protected function call(HttpMethod $method, array $query, array $body, array $headers, array $meta): RouteConnexRun
    {
        $this->recorder()->checkApp($this->client->getApp());

        if ($meta !== []) {
            if ($method === HttpMethod::GET) {
                try {
                    $query['_meta_'] = json_encode($meta, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    throw new InvalidArgumentException('The Meta can\'t be encoded as JSON: '.$e->getMessage(), 0, $e);
                }
            } else {
                $body['_meta_'] = $meta;
            }
        }

        $response = $this->send($method, $query, $body, $headers);

        return $this->result(
            app: $this->client->getApp(),
            service: strtoupper($method->value).' '.$this->sdkEndpoint()::getPath(),
            request: ['query' => $query, 'body' => $body],
            meta: $meta,
            response: $response,
        );
    }

    /**
     * Turn the API's response into the call's result: the Run, stored by
     * `RouteConnexRunRecorder`.
     *
     * @param  string  $service  The Service and HTTP method, e.g. `POST microsoft-sharepoint/files`.
     * @param  array{query: array<string, mixed>, body: array<string, mixed>}  $request
     * @param  array<string, mixed>  $meta
     *
     * @throws RouteConnexRequestException When the answer has no Run ID or an unknown status
     */
    protected function result(RouteConnexAppContract $app, string $service, array $request, array $meta, RouteConnexResponse $response): RouteConnexRun
    {
        return $this->recorder()->record($app, $service, $request, $meta, $response);
    }

    /**
     * The recorder, resolved from the container on every call so a bound
     * subclass replaces it.
     */
    protected function recorder(): RouteConnexRunRecorder
    {
        return app(RouteConnexRunRecorder::class);
    }
}
