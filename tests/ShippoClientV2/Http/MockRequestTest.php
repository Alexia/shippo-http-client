<?php
declare(strict_types=1);

namespace ShippoClientV2\Http;

use PHPUnit\Framework\TestCase;
use ShippoClient\ShippoClientV2;

class MockRequestTest extends TestCase
{
    /**
     * @test
     * @expectedException \ShippoClient\Http\Response\Exception\ServerErrorException
     */
    public function imitateInternalServerError()
    {
        ShippoClientV2::mock()->add('addresses', 500, []);
        ShippoClientV2::provider('anything good because mock works')->addresses()->getList();
    }
}
