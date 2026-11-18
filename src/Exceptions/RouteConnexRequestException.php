<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Exceptions;

use RouteConnex\Laravel\Http\RouteConnexResponse;
use Throwable;

/**
 * The API answered a request with an error status (4xx or 5xx). The response
 * carries the API's error body.
 */
class RouteConnexRequestException extends RouteConnexException
{
    public function __construct(
        protected RouteConnexResponse $response,
        ?Throwable $previous = null,
    ) {
        parent::__construct("The RouteConnex API answered with status {$response->status()}.", 0, $previous);
    }

    public function response(): RouteConnexResponse
    {
        return $this->response;
    }
}
