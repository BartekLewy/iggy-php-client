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


    /**
     * @test
     */
    public function it_returns_200_when_iggy_is_working(): void
    {
        $client = $this->createClient();
        $response = $client->ping();

        $this->assertEquals('pong', $response);
    }

    /**
     * @test
     */
    public function it_returns_tokens_when_authenticate_successfully(): void
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

    /**
     * @test
     */
    public function it_throws_invalid_credentials_exception_when_credentials_are_incorrect(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $client = $this->createClient();
        $client->login('iggy', 'wrong');
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
