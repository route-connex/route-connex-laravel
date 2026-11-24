<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Exceptions;

use Throwable;

/**
 * The RouteConnex API couldn't be reached, or a request failed without a 4xx
 * or 5xx answer (too many redirects, or a transfer that broke, say).
 */
class RouteConnexConnectionException extends RouteConnexException
{
    public function __construct(Throwable $previous)
    {
        parent::__construct("Could not reach the RouteConnex API: {$previous->getMessage()}", 0, $previous);
    }
}
