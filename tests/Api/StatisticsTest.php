<?php
declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Symfony\Component\HttpFoundation\Response;

class StatisticsTest extends ApiTestCase
{
    private const API_URL = '/api/statistics';

    private Client $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetStatistics(): void
    {
        $this->createUrl();

        $this->client->request('GET', self::API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        self::assertJsonContains([
            'totalRedirectCount' => 3,
        ]);
    }

    private function createUrl(): void
    {
        $response = $this->client->request('POST', 'api/urls', [
            'json' => [
                'url' => 'https://www.google.com/',
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $item = $response->toArray();

        var_dump($item);
    }
}
