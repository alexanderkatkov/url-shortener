<?php
declare(strict_types=1);

namespace App\Tests\Api\Urls;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Url;
use Generator;
use Symfony\Component\HttpFoundation\Response;

class UrlsCrudTest extends ApiTestCase
{
    private const API_URL = '/api/urls';

    private Client $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCreateUrl(): void
    {
        $this->client->request('POST', self::API_URL, [
            'json' => [
                'url' => 'https://www.google.com/',
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        self::assertJsonContains([
            'url' => 'https://www.google.com/',
        ]);
        self::assertMatchesResourceItemJsonSchema(Url::class);
    }

    /** @dataProvider createUrlWrongPayloadDataGenerator */
    public function testCreateUrlWrongPayloadFails(array $payload, int $responseCode): void
    {
        $response = $this->client->request('POST', self::API_URL, [
            'json' => $payload,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame($responseCode);
    }

    public function createUrlWrongPayloadDataGenerator(): Generator
    {
        yield [
            'payload' => ['url' => 'random string not url'],
            'responseCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
        ];

        yield [
            'payload' => ['randomkey' => 'random string'],
            'responseCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
        ];

        yield [
            'payload' => ['url' => 1111111],
            'responseCode' => Response::HTTP_BAD_REQUEST,
        ];

        yield [
            'payload' => [],
            'responseCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
        ];
    }

    public function testPutUrlNotAllowed(): void
    {
        $response = $this->client->request('GET', self::API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
        $bookUuid = $response->toArray()[0]['id'];

        $this->client->request('PUT', sprintf('%s/%s', self::API_URL, $bookUuid), [
            'json' => [
                'ulr' => 'www.test.com',
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testGetItem(): void
    {
        $response = $this->client->request('GET', self::API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
        $bookUuid = $response->toArray()[0]['id'];

        $this->client->request('GET', sprintf('%s/%s', self::API_URL, $bookUuid), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        self::assertMatchesResourceItemJsonSchema(Url::class);
    }

    public function testGetItemWithWrongIdFails(): void
    {
        $this->client->request('GET', sprintf(
            '%s/%s',
            self::API_URL,
            '11111111-1111-1111-1111-111111111111'
        ), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testGetUrls(): void
    {
        $response = $this->client->request('GET', self::API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        self::assertCount(1, $response->toArray());
        self::assertMatchesResourceCollectionJsonSchema(Url::class);
    }

    public function testDeleteUrl(): void
    {
        $response = $this->client->request('GET', self::API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $bookUuid = $response->toArray()[0]['id'];

        $this->client->request('DELETE', sprintf('%s/%s', self::API_URL, $bookUuid), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertNull(
            static::getContainer()->get('doctrine')->getRepository(Url::class)->findOneBy(['id' => $bookUuid])
        );
    }
}
