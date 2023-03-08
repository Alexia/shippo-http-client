<?php
declare(strict_types=1);

namespace ShippoClient\Http;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Exception\ClientException as GuzzleClientErrorException;
use GuzzleHttp\Exception\ServerException as GuzzleServerErrorException;
use ShippoClient\Http\Request\MockCollection;
use ShippoClient\Http\Response\Exception\ClientErrorException;
use ShippoClient\Http\Response\Exception\ServerErrorException;

class Request
{
    const BASE_URI = 'https://api.goshippo.com/v1/';

    protected $delegated;
    private $mockContainer;

    public function __construct(string $accessToken, array $options = [])
    {
        $defaultOptions = [
            'base_uri' => static::BASE_URI,
            'headers'  => [
                'Authorization'   => 'ShippoToken ' . $accessToken,
            ],
        ];
        $defaultOptions += $options;

        $this->mockContainer = MockCollection::getInstance();

        if ($this->mockContainer->hasAny()) {
            $defaultOptions['handler'] = $this->mockContainer->getMockHandlerStack();
        }

        $this->delegated = new Client($defaultOptions);
    }

    public function post(string $endPoint, array $body = [])
    {
        $request = new GuzzleRequest(
            'POST',
            $endPoint,
            [
                'form_params' => $body,
            ]
        );
        $guzzleResponse = $this->sendWithCheck($request);

        return json_decode((string)$guzzleResponse->getBody(), true);
    }

    public function postWithJsonBody(string $endPoint, array $body = [])
    {
        $request = new GuzzleRequest(
            'POST',
            $endPoint,
            ['Content-Type' => 'application/json'],
            json_encode($body)
        );
        $guzzleResponse = $this->sendWithCheck($request);

        return json_decode((string)$guzzleResponse->getBody(), true);
    }

    public function get(string $endPoint, array $parameter = [])
    {
        $queryString = http_build_query($parameter);
        $request = new GuzzleRequest('GET', "$endPoint?$queryString");
        $guzzleResponse = $this->sendWithCheck($request);

        return json_decode((string)$guzzleResponse->getBody(), true);
    }

    public function setDefaultOption($keyOrPath, $value)
    {
        $this->delegated->setDefaultOption($keyOrPath, $value);
    }

    private function sendWithCheck(GuzzleRequest $request): GuzzleResponse
    {
        try {
            return $this->delegated->send($request);
        } catch (GuzzleClientErrorException $e) {
            throw new ClientErrorException(
                $e->getMessage(),
                $e->getResponse()->getStatusCode(),
                $e->getRequest(),
                $e->getResponse()
            );
        } catch (GuzzleServerErrorException $e) {
            throw new ServerErrorException(
                $e->getMessage(),
                $e->getResponse()->getStatusCode(),
                $e->getRequest(),
                $e->getResponse()
            );
        }
    }
}
