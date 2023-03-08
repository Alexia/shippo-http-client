<?php

namespace ShippoClient;

use ShippoClient\Http\Request\MockCollection;
use ShippoClient\Http\RequestV2;

class ShippoClientV2
{
    /**
     * @var RequestV2
     */
    private $request;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var static|null
     */
    private static $instance = null;

    private function __construct(RequestV2 $request)
    {
        $this->request = $request;
    }

    public function addresses(): Addresses
    {
        return new Addresses($this->request);
    }

    public function parcels(): Parcels
    {
        return new Parcels($this->request);
    }

    public function shipments(): Shipments
    {
        return new Shipments($this->request);
    }

    public function rates(): Rates
    {
        return new Rates($this->request);
    }

    public function transactions(): Transactions
    {
        return new Transactions($this->request);
    }

    public function refunds(): Refunds
    {
        return new Refunds($this->request);
    }

    public function tracks(): Tracks
    {
        return new Tracks($this->request);
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @param null|string $apiVersion
     * @param array $options Guzzle v7 compatible Client options.
     * @return static
     */
    public static function provider(string $accessToken, ?string $apiVersion = null, array $options = []): self
    {
        if (static::$instance !== null && static::$instance->getAccessToken() === $accessToken) {
            return static::$instance;
        }

        static::$instance = new static(new RequestV2($accessToken, $apiVersion, $options));

        return static::$instance;
    }

    public static function mock()
    {
        return MockCollection::getInstance();
    }
}
