<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use GuzzleHttp\Client;
use RouteConnex\RouteConnexSdkPhp\ErClient;

/**
 * An SDK client whose Guzzle client talks to a `FakeApi` instead of the
 * network. The SDK builds its own Guzzle client, so this is the only way to
 * intercept its requests.
 */
final class FakeErClient extends ErClient
{
    public function useFakeApi(FakeApi $api): self
    {
        $this->client = new Client(['handler' => $api->handlerStack()]);

        return $this;
    }
}
