<?php

declare(strict_types=1);

namespace achertovsky\jwt\Entity;

class Payload
{
    public function __construct(
        private string $id,
        private int $expireAt
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getExpireAt(): int
    {
        return $this->expireAt;
    }
}
