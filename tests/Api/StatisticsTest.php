<?php
declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Url;
use App\Tests\AliceFixtureDependentTestCase;
use App\Tests\Traits\DatabasePurger;
use App\Tests\Traits\GetContainerInstance;
use App\Tests\Traits\GetEntityManager;
use Symfony\Component\HttpFoundation\Response;

class StatisticsTest extends AliceFixtureDependentTestCase
{
    use GetEntityManager, DatabasePurger, GetContainerInstance;

    private const API_URL = '/api/statistics';

    private Client $client;

    protected function getFixtureFiles(): array
    {
        return ['Url.yaml'];
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();

        $this->client = static::createClient();
    }

    public function testGetStatistics(): void
    {
        $response = $this->client->request('GET', self::API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            'totalRedirectCount' => 9,
        ]);
    }
}
