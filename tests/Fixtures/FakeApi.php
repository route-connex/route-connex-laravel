<?php

declare(strict_types=1);

namespace RouteConnex\Laravel\Tests\Fixtures;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Throwable;

/**
 * A stand-in for the RouteConnex API, shared by every SDK client the fake
 * factory builds: it answers from a queue and records the requests it got.
 */
final class FakeApi
{
    private MockHandler $mock;

    /**
     * @var list<array{request: RequestInterface, response: mixed, error: mixed, options: array<string, mixed>}>
     */
    private array $history = [];

    public function __construct()
    {
        $this->mock = new MockHandler;
    }

    public function handlerStack(): HandlerStack
    {
        $stack = HandlerStack::create($this->mock);
        $stack->push(Middleware::history($this->history));

        return $stack;
    }

    /**
     * Queue the answers to the next requests, in order. A throwable is
     * thrown instead of answering, like a failed connection.
     */
    public function queue(Response|Throwable ...$answers): self
    {
        foreach ($answers as $answer) {
            $this->mock->append($answer);
        }

        return $this;
    }

    /**
     * @return list<RequestInterface>
     */
    public function requests(): array
    {
        return array_map(fn (array $entry): RequestInterface => $entry['request'], $this->history);
    }

    /**
     * The requests that were not token requests.
     *
     * @return list<RequestInterface>
     */
    public function serviceRequests(): array
    {
        return array_values(array_filter(
            $this->requests(),
            fn (RequestInterface $request): bool => ! str_ends_with($request->getUri()->getPath(), '/auth/token'),
        ));
    }

    /**
     * The requests to `auth/token`.
     *
     * @return list<RequestInterface>
     */
    public function tokenRequests(): array
    {
        return array_values(array_filter(
            $this->requests(),
            fn (RequestInterface $request): bool => str_ends_with($request->getUri()->getPath(), '/auth/token'),
        ));
    }

    public static function token(string $token = 'test-token'): Response
    {
        return self::json(['access_token' => $token, 'token_type' => 'Bearer', 'expires_in' => 3600]);
    }

    /**
     * The answer to a System call that started a Run: `data.id` and any
     * other `data` fields (`status`, `type`, ...).
     *
     * @param  array<string, mixed>  $data
     */
    public static function run(string $id = 'run-1', array $data = []): Response
    {
        return self::json(['data' => array_merge(['id' => $id], $data)]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function json(array $data = [], int $status = 200): Response
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($data, JSON_THROW_ON_ERROR));
    }
}
