<?php

namespace ShippoClient\Http\Tracks;

use AssertChain\AssertChain;
use ShippoClient\Http\Response\Exception\ClientErrorException;
use ShippoClient\ShippoClient;

class ValidRequestTest extends \PHPUnit_Framework_TestCase
{
    use AssertChain;

    public function testStandAlone()
    {
        ShippoClient::mock()->add('tracks/usps/12345', 200, [
            "tracking_number" => "12345",
            "carrier" =>  "usps",
            "tracking_status" =>  [
                "object_created" => "2014-11-29T16:31:19.511Z",
                "status" => "DELIVERED",
                "status_details" => "Your shipment has been delivered.",
                "status_date" => "2012-03-08T09:58:00Z",
                "location" => [
                    "city" => "Beverly Hills",
                    "state" => "CA",
                    "zip" => "90210",
                    "country" => "US"
                ]
            ],
            "tracking_history" => [
                [
                    "object_created" => "2014-11-29T16:31:19.573Z",
                    "status" => "UNKNOWN",
                    "status_details" => "The electronic shipping data has been received.",
                    "status_date" => "2012-03-06T00:00:00Z",
                    "location" => null,
                ],
                [
                    "object_created" => "2014-11-29T16:31:19.568Z",
                    "status" => "TRANSIT",
                    "status_details" => "Your shipment has been accepted.",
                    "status_date" => "2012-03-06T15:28:00Z",
                    "location" => [
                        "city" => "Las Vegas",
                        "state" => "NV",
                        "zip" => "89121",
                        "country" => "US"
                    ]
                ],
                [
                    "object_created" => "2014-11-29T16:31:19.544Z",
                    "status" => "TRANSIT",
                    "status_details" => "Your shipment has departed the USPS Sort Facility.",
                    "status_date" => "2012-03-07T00:00:00Z",
                    "location" => [
                        "city" => "Bell Gardens",
                        "state" => "CA",
                        "zip" => "90201",
                        "country" => "US"
                    ]
                ],
                [
                    "object_created" => "2014-11-29T16:31:19.539Z",
                    "status" => "TRANSIT",
                    "status_details" => "Your shipment has arrived at the Post Office.",
                    "status_date" => "2012-03-08T04:47:00Z",
                    "location" => [
                        "city" => "Beverly Hills",
                        "state" => "CA",
                        "zip" => "90210",
                        "country" => "US"
                    ]
                ],
                [
                    "object_created" => "2014-11-29T16:31:19.524Z",
                    "status" => "DELIVERED",
                    "status_details" => "Your shipment has been delivered.",
                    "status_date" => "2012-03-08T09:58:00Z",
                    "location" => [
                        "city" => "Beverly Hills",
                        "state" => "CA",
                        "zip" => "90210",
                        "country" => "US"
                    ]
                ]
            ]
        ]);

        $response = ShippoClient::provider('you can use standalone API without accessToken')
            ->tracks()->getStandaloneTrack("usps", '12345');
        $responseArray = $response->toArray();

        $this->assert()
            ->arrayHasKey('carrier', $responseArray)
            ->arrayHasKey('tracking_status', $responseArray)
            ->arrayHasKey('tracking_history', $responseArray)
            ->arrayHasKey('tracking_number', $responseArray);
    }

    /**
     * shippo return 200 even if tracking number is invalid
     */
    public function testCreateWithInvalidTrackingNumber()
    {
        $accessToken = getenv('SHIPPO_PRIVATE_ACCESS_TOKEN');
        $this->assertNotFalse($accessToken, 'You should set env SHIPPO_PRIVATE_ACCESS_TOKEN.');
        $response = ShippoClient::provider($accessToken)->tracks()->create('usps', 12345);

        // shippo return 200 even if tracking number is invalid
        $this->assertInstanceOf('ShippoClient\\Entity\\StandaloneTrack', $response);
    }

    /**
     * shippo return 400 if carrier is invalid
     */
    public function testCreateWithInvalidTrackingCarrier()
    {
        try {
            // shippo return 400 if carrier is invalid
            $accessToken = getenv('SHIPPO_PRIVATE_ACCESS_TOKEN');
            $this->assertNotFalse($accessToken, 'You should set env SHIPPO_PRIVATE_ACCESS_TOKEN.');
            ShippoClient::provider($accessToken)->tracks()->create('invalid carrier', 12345);
            $this->fail('shippo will return 400 if carrier is invalid');
        } catch (ClientErrorException $e) {
            $this->assertSame(400, $e->getStatusCode());
        }
    }
}
