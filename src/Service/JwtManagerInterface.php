<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

use achertovsky\jwt\Entity\Payload;

interface JwtManagerInterface
{
    public function create(Payload $payload): string;
    public function validate(string $token): bool;
    public function decode(string $token): Payload;
}
