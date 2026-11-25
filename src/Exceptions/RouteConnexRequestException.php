<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Exceptions;

use RouteConnex\Laravel\Http\RouteConnexResponse;
use Throwable;

/**
 * The API answered a request with an error status (4xx or 5xx), or with an
 * answer the package can't use, such as a System call without a Run ID. The
 * response carries the API's body.
 */
class RouteConnexRequestException extends RouteConnexException
{
    public function __construct(
        protected RouteConnexResponse $response,
        ?Throwable $previous = null,
        ?string $message = null,
    ) {
        parent::__construct($message ?? "The RouteConnex API answered with status {$response->status()}.", 0, $previous);
    }

    /**
     * A System call answered without a Run ID in `data.id`.
     */
    public static function noRunId(RouteConnexResponse $response): self
    {
        return new self($response, message: 'The RouteConnex API answered without a Run ID.');
    }

    /**
     * A Run status the package doesn't know.
     */
    public static function unknownRunStatus(RouteConnexResponse $response, mixed $status): self
    {
        $status = is_scalar($status) ? (string) $status : get_debug_type($status);

        return new self($response, message: "The RouteConnex API answered with an unknown Run status [{$status}].");
    }

    public function response(): RouteConnexResponse
    {
        return $this->response;
    }
}
