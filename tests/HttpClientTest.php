<?php

declare(strict_types=1);

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

    /**
     * @test
     */
    public function it_returns_user(): void
    {
        $client = $this->createClient();
        $response = $client->login('iggy', 'iggy');

        $user = $client->getUser($response['user_id'], $response['tokens']['access_token']['token']);

        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('created_at', $user);
        $this->assertArrayHasKey('status', $user);
        $this->assertArrayHasKey('username', $user);
        $this->assertArrayHasKey('permissions', $user);

        $this->assertArrayHasKey('global', $user['permissions']);
        $this->assertArrayHasKey('streams', $user['permissions']);
     }

    /**
     * @test
     */
    public function it_throws_user_not_found_exception_when_user_does_not_exist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Resource with key: users:9999 was not found');

        $client = $this->createClient();
        $response = $client->login('iggy', 'iggy');

        $client->getUser(9999, $response['tokens']['access_token']['token']);
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
