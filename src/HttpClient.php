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
}
