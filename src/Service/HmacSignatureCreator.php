<?php

declare(strict_types=1);

namespace achertovsky\jwt\Service;

class HmacSignatureCreator
{
    public function __construct(
        private string $algorithm,
        private string $secretKey,
    ) {
    }

    public function sign(string $data): string
    {
        return hash_hmac(
            $this->algorithm,
            $data,
            $this->secretKey
        );
    }
}
