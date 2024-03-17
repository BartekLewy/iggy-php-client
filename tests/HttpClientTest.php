<?php

namespace Iggy\Tests;

use Iggy\HttpClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Psr18Client;

class HttpClientTest extends TestCase
{
    private const BASE_URL = 'http://localhost';
    private const PORT = 3000;

    public function test_login(): void
    {
        $client = $this->createClient();
        $response = $client->login('iggy', 'iggy');


        $this->assertArrayHasKey('user_id', $response);
        $this->assertArrayHasKey('tokens', $response);

        $this->assertArrayHasKey('access_token', $response['tokens']);
        $this->assertArrayHasKey('refresh_token', $response['tokens']);

        $this->assertArrayHasKey('token', $response['tokens']['access_token']);
        $this->assertArrayHasKey('expiry', $response['tokens']['access_token']);

        $this->assertArrayHasKey('token', $response['tokens']['refresh_token']);
        $this->assertArrayHasKey('expiry', $response['tokens']['refresh_token']);
    }

    private function createClient(): HttpClient
    {
        return new HttpClient(
            new Psr18Client(),
            new Psr17Factory(),
            self::BASE_URL,
            self::PORT
        );
    }
}
