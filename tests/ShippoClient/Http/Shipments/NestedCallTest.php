<?php
declare(strict_types=1);

namespace ShippoClient\Http\Shipments;

use PHPUnit\Framework\TestCase;
use ShippoClient\ShippoClient;

class NestedCallTest extends TestCase
{
    private $accessToken;

    public function setUp(): void
    {
        $this->accessToken = getenv('SHIPPO_PRIVATE_ACCESS_TOKEN');
    }

    public function testCreateShipmentByNestedCall()
    {
        $this->assertNotFalse($this->accessToken, 'You should set env SHIPPO_PRIVATE_ACCESS_TOKEN.');

        $param = [
            "object_purpose" => "PURCHASE",
            "address_from" => [
                "object_purpose" => "PURCHASE",
                "name"           => "Mr. Hippo",
                "company"        => "Shippo",
                "street1"        => "215 Clayton St.",
                "street2"        => "",
                "city"           => "San Francisco",
                "state"          => "CA",
                "zip"            => "94117",
                "country"        => "US",
                "phone"          => "+1 555 341 9393",
                "email"          => "api@goshippo.com"
            ],
            "address_to" => [
                "object_purpose" => "PURCHASE",
                "name"           => "Mrs. Hippo",
                "company"        => "Shippo",
                "street1"        => "Mission St.",
                "street_no"      => "814",
                "street2"        => "",
                "city"           => "San Francisco",
                "state"          => "CA",
                "zip"            => "94105",
                "country"        => "US",
                "phone"          => "+1 555 341 9393",
                "email"          => "support@goshippo.com",
                "metadata"       => "Customer ID 123456"
            ],
            "parcel" => [
                "length"        => "5",
                "width"         => "5",
                "height"        => "5",
                "distance_unit" => "in",
                "weight"        => "2",
                "mass_unit"     => "lb",
                "template"      => "",
                "metadata"      => "Customer ID 123456"
            ],
            "reference_1" => "Created on",
            "reference_2" => "Shippo",
            "metadata" => "Customer ID 123456"
        ];

        $shipment = ShippoClient::provider($this->accessToken)->shipments()->createByNestedCall($param);
        $this->assertInstanceOf('ShippoClient\\Entity\\Shipment', $shipment);
        $shipmentArray = $shipment->toArray();
        $this->assertIsArray($shipmentArray['carrier_accounts']);
        $this->assertInstanceOf('\\DateTime', $shipmentArray['object_created']);
        $this->assertInstanceOf('\\DateTime', $shipmentArray['object_updated']);
        $this->assertNotEmpty($shipmentArray['object_id']);
        $this->assertNotEmpty($shipmentArray['object_owner']);
        $this->assertSame('VALID', $shipmentArray['object_state']);
        $this->assertSame('QUEUED', $shipmentArray['object_status']);
        $this->assertSame('PURCHASE', $shipmentArray['object_purpose']);
        $this->assertNotEmpty($shipmentArray['address_from']);
        $this->assertNotEmpty($shipmentArray['address_to']);
        $this->assertNotEmpty($shipmentArray['parcel']);
        $this->assertNotEmpty($shipmentArray['submission_type']);
        $this->assertRegExp('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $shipmentArray['submission_date']);
        $this->assertNotEmpty($shipmentArray['address_return']);
        $this->assertSame('', $shipmentArray['return_of']);
        $this->assertSame('', $shipmentArray['customs_declaration']);
        $this->assertSame(0, $shipmentArray['insurance_amount']);
        $this->assertSame('', $shipmentArray['insurance_currency']);
        $this->assertIsArray($shipmentArray['extra']);
        $this->assertArrayHasKey('reference_1', $shipmentArray);
        $this->assertArrayHasKey('reference_2', $shipmentArray);
        $this->assertNotEmpty($shipmentArray['rates_url']);
        $this->assertIsArray($shipmentArray['messages']);
        $this->assertSame('Customer ID 123456', $shipmentArray['metadata']);
    }
}
