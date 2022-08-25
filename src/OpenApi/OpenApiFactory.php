<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{

    public function __construct(private OpenApiFactoryInterface $decorated){

    }


    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        foreach ($openApi->getPaths()->getPaths() as $key => $path){
            if($path->getGet() && $path->getGet()->getSummary() === 'hidden'){
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }
        $openApi->getComponents()->getSchemas()["User-write.user"]["properties"]["password"]["example"] = '$2y$13$d54zhWFIupqc/cw7Ybv...9mqHtGY27U9pa1vidIxrp8/I0oEXQdW';
        $openApi->getComponents()->getSchemas()["User.jsonld-write.user"]["properties"]["password"]["example"] = '$2y$13$d54zhWFIupqc/cw7Ybv...9mqHtGY27U9pa1vidIxrp8/I0oEXQdW';

        #Delete Id parameter for /api/me path
        $meOperation = $openApi->getPaths()->getPath("/api/me")->getGet()->withParameters([]);
        $meItem = $openApi->getPaths()->getPath("/api/me")->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/me', $meItem);

        #Delete Id parameter for /api/users post path
        $usersPostOperation = $openApi->getPaths()->getPath("/api/users")->getPut()->withParameters([]);
        $usersPostPath = $openApi->getPaths()->getPath("/api/users")->withPut($usersPostOperation);
        $openApi->getPaths()->addPath('/api/users', $usersPostPath);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'test'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'mdptest'
                ]
            ]
        ]);

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true
                ]
            ]
        ]);

        $schemas['Refresh_Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'fca6c09862be8c715ee461bd583320118aa5ff73cb5cc3241ec165352a2e8fa0d6f79d76642fcd98805933b9784c5f29c822b85eec77a531e44831c6026ed3c4',
                ]
            ]
        ]);



        $schema = $openApi->getComponents()->getSecuritySchemes();
        $schema['bearerAuth'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ]);
        $openApi = $openApi->withSecurity([['bearerAuth' => []]]);

        $pathItemlogin = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ["Auth"],
                responses: [
                    '200' => [
                        'description' => 'Token JWT',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token'
                                ]
                            ]
                        ]
                    ]
                ],
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                    'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                ),
                security: []
            )
        );

        $pathItemlogout = new PathItem(
            post: new Operation(
                operationId: 'postApiLogout',
                tags: ["Auth"],
                responses: [
                    '200' => [
                        'description' => 'The supplied refresh_token has been invalidated.'
                    ],
                    '400' => [
                        'description' => 'No refresh_token found.'
                    ]
                ],
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Refresh_Token'
                            ]
                        ]
                    ])
                ),
                security: []
            )
        );

        $openApi->getPaths()->addPath('/api/login', $pathItemlogin);
        $openApi->getPaths()->addPath('/api/token/invalidate', $pathItemlogout);


        return $openApi;
    }
}