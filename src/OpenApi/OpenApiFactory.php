<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{

    private function PostWithNoneParameter(OpenApi $openApi, string $path): OpenApi
    {
        $operation = $openApi->getPaths()->getPath($path)->getPost()->withParameters([]);
        $pathItem = $openApi->getPaths()->getPath($path)->withPost($operation);
        $openApi->getPaths()->addPath($path,$pathItem);
        return $openApi;
    }

    private function GetWithNoneParameter(OpenApi $openApi, string $path): OpenApi
    {
        $operation = $openApi->getPaths()->getPath($path)->getGet()->withParameters([]);
        $pathItem = $openApi->getPaths()->getPath($path)->withGet($operation);
        $openApi->getPaths()->addPath($path,$pathItem);
        return $openApi;
    }

    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        /**
         * @var PathItem $path
         */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        };

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postLogin',
                tags: ['Authentication'],
                responses: [
                    '200' => [
                        'description' => 'Utilisateur connectée',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => [
                                            'type' => 'string',
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                summary: 'ce connecter',
                requestBody: new RequestBody(
                    description: 'Email et mot de passe',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'username' => [
                                        'type' => 'string',
                                        'example' => 'maxime.wiatr@gmail.com'
                                    ],
                                    'password' => [
                                        'type' => 'string',
                                        'example' => '1234',
                                    ]
                                ],
                                'required' => [
                                    'username',
                                    'password',
                                ]
                            ]
                        ]
                    ]),
                    required: true,
                )
            ),
        );

        $openApi->getPaths()->addPath('/api/login',$pathItem);
        $schemas = $openApi->getComponents()->getSecuritySchemes();

        $schemas['JWT'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ]);

        return $openApi;
    }
}