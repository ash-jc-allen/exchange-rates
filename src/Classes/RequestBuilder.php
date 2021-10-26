<?php

namespace AshAllenDesign\ExchangeRates\Classes;

use GuzzleHttp\Client;

class RequestBuilder
{
    private const BASE_URL = 'https://api.exchangerate.host/';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function makeRequest(string $path, array $queryParams = []): array
    {
        $url = static::BASE_URL.$path.'?'.http_build_query($queryParams);

        return json_decode(
            $this->client->get($url)->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
