<?php
declare(strict_types=1);

namespace ShippoClient\Http\Request;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class MockCollection
{
    /**
     * @var static|null
     */
    private static $instance = null;

    private static $container = [];

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance !== null) {
            return self::$instance;
        }
        self::$instance = new self();

        return self::$instance;
    }

    public function has(string $endPoint): bool
    {
        return array_key_exists($endPoint, self::$container);
    }

    public function hasAny(): bool {
        return count(self::$container) > 0;
    }

    /**
     * @param string $endPoint
     * @return HandlerStack
     */
    public function getMockHandlerStack(): HandlerStack
    {
        $responses = [];
        foreach (self::$container as $endPoint => $options) {
            $responses[] = new Response($options['statusCode'], [], json_encode($options['response']));
        }

        $mockHandler = new MockHandler(
            $responses
        );
        $handlerStack = HandlerStack::create($mockHandler);

        return $handlerStack;
    }

    public function add(string $path, int $statusCode, array $response): self
    {
        self::$container[$path] = [
            'statusCode' => $statusCode,
            'response'   => $response,
        ];

        return $this;
    }

    public function clear(): void
    {
        self::$container = [];
    }
}
