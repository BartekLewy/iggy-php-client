<?php

declare(strict_types=1);

namespace Iggy;

use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final readonly class HttpClient implements ClientInterface
{
    private const HTTP_BAD_REQUEST = 400;

    public function __construct(
        private PsrClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private string $baseUrl,
        private int $port = 3000,
    ) {
    }

    public function ping(): string
    {
        $request = $this->requestFactory->createRequest(
            'GET',
            sprintf('%s:%s/ping', $this->baseUrl, $this->port)
        );

        $response = $this->client->sendRequest($request);

        return $response->getBody()->getContents();
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
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

        if ($response->getStatusCode() === self::HTTP_BAD_REQUEST) {
            throw new \RuntimeException($response->getBody()->getContents());
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getUser(int $id, string $token): array
    {
        $request = $this->requestFactory->createRequest(
            'GET',
            sprintf('%s:%s/users/%s', $this->baseUrl, $this->port, $id)
        );
        $request = $request->withHeader('Authorization', sprintf('Bearer %s', $token));

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() === 404) {
            $response = json_decode($response->getBody()->getContents(), true);
            throw new \RuntimeException($response['reason']);
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
