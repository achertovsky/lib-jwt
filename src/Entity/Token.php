<?php

declare(strict_types=1);

namespace achertovsky\jwt\Entity;

class Token
{
    public function __construct(
        private string $header,
        private string $payload
    ) {
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }
}
