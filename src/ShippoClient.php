<?php
namespace ShippoClient;

use ShippoClient\Http\Request;
use ShippoClient\Http\Request\MockCollection;

class ShippoClient
{
    /**
     * @var Request
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

    private function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function addresses()
    {
        return new Addresses($this->request);
    }

    public function parcels()
    {
        return new Parcels($this->request);
    }

    public function shipments()
    {
        return new Shipments($this->request);
    }

    public function rates()
    {
        return new Rates($this->request);
    }

    public function transactions()
    {
        return new Transactions($this->request);
    }

    public function refunds()
    {
        return new Refunds($this->request);
    }

    public function tracks()
    {
        return new Tracks($this->request);
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return static
     */
    public static function provider($accessToken)
    {
        if (static::$instance !== null && static::$instance->getAccessToken() === $accessToken) {
            return static::$instance;
        }

        static::$instance = new static(new Request($accessToken));

        return static::$instance;
    }

    public static function mock()
    {
        return MockCollection::getInstance();
    }
}
