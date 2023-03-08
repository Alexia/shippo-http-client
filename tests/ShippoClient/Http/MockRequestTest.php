<?php
declare(strict_types=1);

namespace ShippoClient\Http;

use PHPUnit\Framework\TestCase;
use ShippoClient\ShippoClient;

class MockRequestTest extends TestCase
{
    /**
     * @test
     * @expectedException \ShippoClient\Http\Response\Exception\ServerErrorException
     */
    public function imitateInternalServerError()
    {
        ShippoClient::mock()->add('addresses', 500, []);
        ShippoClient::provider('anything good because mock works')->addresses()->getList();
    }
}
