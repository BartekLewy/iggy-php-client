<?php

declare(strict_types=1);

namespace Iggy;

interface ClientInterface
{
    public function login(string $username, string $password): array;
}
