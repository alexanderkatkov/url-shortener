<?php
declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $openApi->getPaths()->addPath(
            '/api/statistics',
            new PathItem(null, null, null, new Operation(
                'get',
                ['Statistics'],
                [
                    Response::HTTP_OK => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'totalRedirectCount' => [
                                            'type' => 'integer',
                                            'example' => 997,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ))
        );

        return $openApi;
    }
}
