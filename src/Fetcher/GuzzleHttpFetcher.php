<?php

namespace Yosefda\Recommendation\Fetcher;

use GuzzleHttp\Client;

class GuzzleHttpFetcher implements IFetcher
{
    /**
     * @var Client
     * GuzzleHttp client.
     */
    protected $client;

    /**
     * GuzzleHttpFetcher constructor.
     * @param Client $guzzle
     */
    public function __construct(Client $guzzle)
    {
        $this->client = $guzzle;
    }

    /**
     * @param string $uri URI of the data source
     * @return string Content of the data source
     * @throws \RuntimeException
     */
    public function fetch(string $uri)
    {
        try {
            $response = $this->client->get($uri);
            $code = $response->getStatusCode();
            if ($code === 200) {
                return (string) $response->getBody();
            } else {
                throw new \RuntimeException("HTTP status code {$code} returned");
            }
        } catch (\Exception $ex) {
            throw new \RuntimeException("Failed to fetch {$uri}, reason: {$ex->getMessage()}");
        }
    }
}