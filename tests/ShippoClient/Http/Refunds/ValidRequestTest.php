<?php
declare(strict_types=1);

namespace ShippoClient\Http\Request\Refunds;

use PHPUnit\Framework\TestCase;
use AssertChain\AssertChain;
use ShippoClient\ShippoClient;

/**
 * refunds domain is required purchased account.
 */
class ValidRequestTest extends TestCase
{
    use AssertChain;

    /**
     * @test
     */
    public function createRefund()
    {
        ShippoClient::mock()->add("refunds", 200, [
            "object_created" =>  "2014-04-21T07:12:41.044Z",
            "object_updated" =>  "2014-04-21T07:12:41.045Z",
            "object_id"      =>  "bd7b8379a2e847bcb0818125943dde5d",
            "object_owner"   =>  "tech@goshippo.com",
            "object_status"  =>  "QUEUED",
            "transaction"    =>  "35ed59f23a514ecfa2faeaed93a00086"
        ]);

        $refund = ShippoClient::provider('dummy access token')->refunds()->create('dummy transaction object id');
        $this->assertInstanceOf('ShippoClient\\Entity\\Refund', $refund);
        $refundArray = $refund->toArray();
        $this->assert()
            ->instanceOf('\\DateTime', $refundArray['object_created'])
            ->instanceOf('\\DateTime', $refundArray['object_updated'])
            ->notempty($refundArray['object_id'])
            ->notempty($refundArray['object_owner'])
            ->same("35ed59f23a514ecfa2faeaed93a00086", $refundArray['transaction'])
            ->notEmpty($refundArray['object_status']);
    }

    /**
     * @test
     */
    public function retrieveRefund()
    {
        $dummyObjectId = 'dummy object id';
        $mockObject = [
            "object_created" =>  "2014-04-21T07:12:41.044Z",
            "object_updated" =>  "2014-04-21T07:12:41.045Z",
            "object_id"      =>  "bd7b8379a2e847bcb0818125943dde5d",
            "object_owner"   =>  "tech@goshippo.com",
            "object_status"  =>  "QUEUED",
            "transaction"    =>  "35ed59f23a514ecfa2faeaed93a00086"
        ];
        ShippoClient::mock()->add("refunds/" . $dummyObjectId, 200, $mockObject);

        $refundResponse = ShippoClient::provider('dummy access token')->refunds()->retrieve($dummyObjectId);
        $refundArray = $refundResponse->toArray();
        $this->assert()
            ->instanceOf('\\DateTime', $refundArray['object_created'])
            ->instanceOf('\\DateTime', $refundArray['object_updated'])
            ->same($mockObject['object_id'], $refundArray['object_id'])
            ->same($mockObject['object_owner'], $refundArray['object_owner'])
            ->same($mockObject['object_status'], $refundArray['object_status'])
            ->same($mockObject['transaction'], $refundArray['transaction']);
    }

    /**
     * @test
     */
    public function getRefundList()
    {
        ShippoClient::mock()->add("refunds", 200, [
            'count'    => 1,
            'next'     => null,
            'previous' => null,
            'results'  => [
                [
                    "object_created" =>  "2014-04-21T07:12:41.044Z",
                    "object_updated" =>  "2014-04-21T07:12:41.045Z",
                    "object_id"      =>  "bd7b8379a2e847bcb0818125943dde5d",
                    "object_owner"   =>  "tech@goshippo.com",
                    "object_status"  =>  "QUEUED",
                    "transaction"    =>  "35ed59f23a514ecfa2faeaed93a00086"
                ],
            ]
        ]);

        $refund = ShippoClient::provider('dummy access token')->refunds()->getList();
        $this->assertInstanceOf('ShippoClient\\Http\\Response\\RefundList', $refund);
        $responseArray = $refund->toArray();
        $this->assert()
            ->greaterThanOrEqual(1, $responseArray['count'])
            ->arrayHasKey('next', $responseArray)
            ->arrayHasKey('previous', $responseArray)
            ->containsOnlyInstancesOf('ShippoClient\\Entity\\Refund', $refund->getResults()->getArrayCopy());
    }
}
