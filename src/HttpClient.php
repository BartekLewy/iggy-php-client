<?php

declare(strict_types=1);

namespace Iggy;

use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

readonly class HttpClient implements ClientInterface
{
    public function __construct(
        private PsrClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private string $baseUrl,
        private int $port = 3000
    ) {
    }

    public function login(string $username, string $password): array
    {
        $request = $this->requestFactory->createRequest(
            'POST',
            sprintf('%s:%s/users/login', $this->baseUrl, $this->port)
        );
        $request->getBody()->write(json_encode(['username' => $username, 'password' => $password]));
        $request->getBody()->rewind();
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->client->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }
}
