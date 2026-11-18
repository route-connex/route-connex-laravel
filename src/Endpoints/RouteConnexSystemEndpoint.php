<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Endpoints;

use InvalidArgumentException;
use JsonException;
use RouteConnex\Laravel\Apps\RouteConnexConfigApp;
use RouteConnex\Laravel\Http\RouteConnexResponse;
use RouteConnex\RouteConnexSdkPhp\HttpMethod;

/**
 * An endpoint of a System (Microsoft SharePoint, HPE Content Manager). It
 * creates a Run, so it takes Meta and hands its response to `result()`.
 */
abstract class RouteConnexSystemEndpoint extends RouteConnexEndpoint
{
    /**
     * Put the Meta where the API expects it (`_meta_` as JSON in the query for
     * `GET`, as an array in the body otherwise), send the request and return
     * the call's result.
     *
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>  $body
     * @param  array<string, string>  $headers
     * @param  array<string, mixed>  $meta
     *
     * @throws InvalidArgumentException When the Meta can't be encoded as JSON
     */
    protected function call(HttpMethod $method, array $query, array $body, array $headers, array $meta): RouteConnexResponse
    {
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
     * Turn the API's response into the call's result. It returns the response
     * itself for now; `RouteConnexRun` tracking changes this step to store
     * and return the Run.
     *
     * @param  string  $service  The Service and HTTP method, e.g. `POST microsoft-sharepoint/files`.
     * @param  array{query: array<string, mixed>, body: array<string, mixed>}  $request
     * @param  array<string, mixed>  $meta
     */
    protected function result(RouteConnexConfigApp $app, string $service, array $request, array $meta, RouteConnexResponse $response): RouteConnexResponse
    {
        return $response;
    }
}
