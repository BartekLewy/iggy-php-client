<?php

declare(strict_types=1);

namespace Iggy;

interface ClientInterface
{
    public function ping(): string;

    public function login(string $username, string $password): array;

    public function getUser(int $id, string $token): array;
}
