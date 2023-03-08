<?php
declare(strict_types=1);

namespace ShippoClient\Http\Request\Rates;

use PHPUnit\Framework\TestCase;
use AssertChain\AssertChain;
use ShippoClient\ShippoClient;

/**
 * Rates objects are created asynchronously.
 * Http request is mocked.
 */
class ValidRequestTest extends TestCase
{
    use AssertChain;

    private static $dummyRateObject = [
        "object_state"             => "VALID",
        "object_purpose"           => "PURCHASE",
        "object_created"           => "2014-07-17T00:04:10.118Z",
        "object_updated"           => "2014-07-17T00:04:10.627Z",
        "object_id"                => "ee81fab0372e419ab52245c8952ccaeb",
        "object_owner"             => "unittest",
        "shipment"                 => "89436997a794439ab47999701e60392e",
        "attributes"               => [],
        "amount"                   => "5.35",
        "currency"                 => "USD",
        "amount_local"             => "5.35",
        "currency_local"           => "USD",
        "provider"                 => "USPS",
        "provider_image_75"        => "https://shippo-static.s3.amazonaws.com/providers/75/USPS.png",
        "provider_image_200"       => "https://shippo-static.s3.amazonaws.com/providers/200/USPS.png",
        "servicelevel_name"        => "Priority Mail",
        "servicelevel_terms"       => "",
        'available_shippo'         => true,
        "days"                     => 1,
        "duration_terms"           => "Delivery within 1, 2, or 3 days based on where your package started and where it’s being sent.",
        "trackable"                => true,
        "insurance"                => true,
        "insurance_amount_local"   => "50.00",
        "insurance_currency_local" => "USD",
        "insurance_amount"         => "50.00",
        "insurance_currency"       => "USD",
        "carrier_account"          => "b741b99f95e841639b54272834bc478c",
        "arrives_by"               => "arrives_by",
        "messages"                 => [],
        'delivery_attempts'        => '',
        'outbound_endpoint'        => '',
        'inbound_endpoint'         => '',
    ];

    /**
     * @test
     */
    public function getListOfRateByShipment()
    {
        $dummyObjectId = 'dummy_object_id';
        ShippoClient::mock()->add("shipments/$dummyObjectId/rates/USD", 200, [
            'count' => 1,
            'next'  => null,
            'previous' => null,
            'results' => [self::$dummyRateObject]
        ]);

        $response = ShippoClient::provider('dummy access token')->shipments()->getRateList($dummyObjectId, 'USD');
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\RateList', $response);
        $responseArray = $response->toArray();
        $this->assert()
            ->greaterThanOrEqual(1, $responseArray['count'])
            ->arrayHasKey('next', $responseArray)
            ->arrayHasKey('previous', $responseArray)
            ->containsOnlyInstancesOf('ShippoClient\\Entity\\Rate', $response->getResults()->getArrayCopy());
    }

    /**
     * @test
     */
    public function retrieveRate()
    {
        ShippoClient::mock()->add('rates/dummy_object_id', 200, self::$dummyRateObject);

        $response = ShippoClient::provider('dummy access token')->rates()->retrieve('dummy_object_id');
        $this->assertInstanceOf('ShippoClient\\Entity\\Rate', $response);
        $responseArray = $response->toArray();
        $this->assert()
            ->same("VALID", $responseArray['object_state'])
            ->same("PURCHASE", $responseArray['object_purpose'])
            ->instanceOf('\\DateTime', $responseArray['object_created'])
            ->instanceOf('\\DateTime', $responseArray['object_updated'])
            ->notEmpty($responseArray['object_id'])
            ->notEmpty($responseArray['object_owner'])
            ->notEmpty($responseArray['shipment'])
            ->true($responseArray['available_shippo'])
            ->isArray($responseArray['attributes'])
            ->isFloat($responseArray['amount'])
            ->same("USD", $responseArray['currency'])
            ->isFloat($responseArray['amount_local'])
            ->same("USPS", $responseArray['provider'])
            ->notEmpty($responseArray['provider_image_75'])
            ->notEmpty($responseArray['provider_image_200'])
            ->arrayHasKey('servicelevel_name', $responseArray)
            ->arrayHasKey('servicelevel_terms', $responseArray)
            ->isInt($responseArray['days'])
            ->arrayHasKey('arrives_by', $responseArray)
            ->notEmpty($responseArray['duration_terms'])
            ->true($responseArray['trackable'])
            ->true($responseArray['insurance'])
            ->same(50.0, $responseArray['insurance_amount'])
            ->same('USD', $responseArray['insurance_currency'])
            ->same(50.0, $responseArray['insurance_amount_local'])
            ->same('USD', $responseArray['insurance_currency_local'])
            ->arrayHasKey('delivery_attempts', $responseArray)
            ->arrayHasKey('outbound_endpoint', $responseArray)
            ->arrayHasKey('inbound_endpoint', $responseArray)
            ->isArray($responseArray['messages'])
            ->notEmpty($responseArray['carrier_account']);
    }

    /**
     * @test
     */
    public function getRateList()
    {
        ShippoClient::mock()->add('rates', 200, [
            'count' => 1,
            'next'  => null,
            'previous' => null,
            'results' => [self::$dummyRateObject]
        ]);

        $response = ShippoClient::provider('dummy access token')->rates()->getList();
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\RateList', $response);
        $responseArray = $response->toArray();
        $this->assert()
            ->greaterThanOrEqual(1, $responseArray['count'])
            ->arrayHasKey('next', $responseArray)
            ->arrayHasKey('previous', $responseArray)
            ->containsOnlyInstancesOf('ShippoClient\\Entity\\Rate', $response->getResults()->getArrayCopy());
    }
}
