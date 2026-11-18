<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Exceptions;

use RouteConnex\Laravel\Http\RouteConnexResponse;
use Throwable;

/**
 * The token request failed or issued no usable token: the API rejected the
 * App's Credentials, or answered without a valid token.
 * It's thrown only while getting a token: an error answer to a Service call
 * is a `RouteConnexRequestException`, even a 401 or 403.
 */
class RouteConnexAuthenticationException extends RouteConnexException
{
    public function __construct(
        string $message,
        protected ?RouteConnexResponse $response = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * The API answered the token request with an error.
     */
    public static function rejected(RouteConnexResponse $response, ?Throwable $previous = null): self
    {
        return new self("The RouteConnex API answered the token request with status {$response->status()}.", $response, $previous);
    }

    /**
     * The API answered the token request, but without a usable token.
     */
    public static function noToken(?Throwable $previous = null): self
    {
        return new self('The RouteConnex API issued no usable access token.', previous: $previous);
    }

    /**
     * The API's answer to the token request, when there is one.
     */
    public function response(): ?RouteConnexResponse
    {
        return $this->response;
    }
}
