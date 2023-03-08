<?php

namespace ShippoClient\Http;

class RequestV2 extends Request
{
    const BASE_URI = 'https://api.goshippo.com/';

    /**
     * @param string $accessToken
     * @param null|string $apiVersion
     * @param array $options Guzzle v7 compatible Client options.
     */
    public function __construct(string $accessToken, ?string $apiVersion = null, array $options = [])
    {
        if ($apiVersion) {
            $options['headers']['Shippo-API-Version'] = $apiVersion;
        }

        parent::__construct($accessToken, $options);
    }
}
