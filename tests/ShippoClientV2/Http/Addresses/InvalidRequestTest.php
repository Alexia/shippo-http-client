<?php
declare(strict_types=1);

namespace ShippoClientV2\Http\Addresses;

use PHPUnit\Framework\TestCase;
use ShippoClient\Http\Response\Exception\ClientErrorException;
use ShippoClient\ShippoClientV2;

/**
 * @property string accessToken
 */
class InvalidRequestTest extends TestCase
{
    public function setUp(): void
    {
        $this->accessToken = getenv('SHIPPO_PRIVATE_ACCESS_TOKEN');
    }

    public function testWithInvalidPrivateToken()
    {
        try {
            ShippoClientV2::provider('invalid token')->addresses()->getList();
        } catch (ClientErrorException $e) {
            $this->assertSame(401, $e->getStatusCode());
            $this->assertNotEmpty($e->getMessage());
            $this->assertIsObject($e->getRequest());
            $this->assertIsObject($e->getResponse());
        }
    }

    public function testRetrieveWithInvalidObjectId()
    {
        $this->assertNotFalse($this->accessToken, 'You should set env SHIPPO_PRIVATE_ACCESS_TOKEN.');
        try {
            ShippoClientV2::provider($this->accessToken)->addresses()->retrieve('invalid objectId');
            $this->fail('ClientErrorException is expected');
        } catch (ClientErrorException $e) {
            $this->assertSame(404, $e->getStatusCode());
            $this->assertNotEmpty($e->getMessage());
            $this->assertIsObject($e->getRequest());
            $this->assertIsObject($e->getResponse());
        }
    }

    public function testValidateWithInvalidObjectId()
    {
        $this->assertNotFalse($this->accessToken, 'You should set env SHIPPO_PRIVATE_ACCESS_TOKEN.');
        try {
            ShippoClientV2::provider($this->accessToken)->addresses()->validate('invalid objectId');
            $this->fail('ClientErrorException is expected');
        } catch (ClientErrorException $e) {
            $this->assertSame(404, $e->getStatusCode());
            $this->assertNotEmpty($e->getMessage());
            $this->assertIsObject($e->getRequest());
            $this->assertIsObject($e->getResponse());
        }
    }
}
